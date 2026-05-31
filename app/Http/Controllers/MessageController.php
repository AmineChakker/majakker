<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreMessageRequest;
use App\Models\{Message, User, Group};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MessageController extends Controller
{
    public function index(Request $request)
    {
        $me = $request->user();

        // Existing conversations
        $conversations = User::whereIn('id', function ($q) use ($me) {
            $q->select(DB::raw('CASE WHEN sender_id = ' . $me->id . ' THEN recipient_id ELSE sender_id END'))
              ->from('messages')
              ->where(fn($q2) => $q2->where('sender_id', $me->id)->orWhere('recipient_id', $me->id));
        })->get()->map(function ($u) use ($me) {
            $u->last_message = Message::where(fn($q) => $q->where('sender_id', $me->id)->where('recipient_id', $u->id))
                ->orWhere(fn($q) => $q->where('sender_id', $u->id)->where('recipient_id', $me->id))
                ->latest()->first();
            return $u;
        })->sortByDesc(fn($u) => $u->last_message?->created_at)->values();

        // Available contacts (role-scoped)
        $contacts = $this->getAllowedContacts($me);

        return view('messages.index', compact('conversations', 'contacts'));
    }

    public function conversation(User $user, Request $request)
    {
        $me = $request->user();

        // Enforce: can this user message $user?
        abort_unless($this->canMessage($me, $user), 403, 'Vous ne pouvez pas contacter cet utilisateur.');

        $messages = Message::where(fn($q) => $q->where('sender_id', $me->id)->where('recipient_id', $user->id))
            ->orWhere(fn($q) => $q->where('sender_id', $user->id)->where('recipient_id', $me->id))
            ->oldest()->get();

        Message::where('sender_id', $user->id)->where('recipient_id', $me->id)
            ->whereNull('read_at')->update(['read_at' => now()]);

        $contacts = $this->getAllowedContacts($me);

        return view('messages.show', compact('user', 'messages', 'contacts'));
    }

    public function send(User $user, StoreMessageRequest $request)
    {
        $me = $request->user();

        abort_unless($this->canMessage($me, $user), 403, 'Vous ne pouvez pas contacter cet utilisateur.');

        Message::create([
            'sender_id'    => $me->id,
            'recipient_id' => $user->id,
            'body'         => $request->body,
        ]);

        return back();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function canMessage(User $from, User $to): bool
    {
        if ($from->id === $to->id) return false;

        // Admin can message anyone
        if ($from->isAdmin()) return true;

        // Both must belong to a school
        if (!$from->school_id || !$to->school_id) return false;

        // Must be same school
        if ($from->school_id !== $to->school_id) return false;

        if ($from->isTeacher()) {
            // Teacher → other teacher / director in same school ✓
            if ($to->isTeacher() || $to->isDirector()) return true;

            // Teacher → student only if student is in one of the teacher's classes
            if ($to->isStudent()) {
                return $this->isStudentInTeacherClasses($from, $to);
            }
            return false;
        }

        // Director, students: can message anyone in same school
        return true;
    }

    private function isStudentInTeacherClasses(User $teacher, User $student): bool
    {
        $classIds = Group::where('school_id', $teacher->school_id)
            ->where('kind', 'class')
            ->where('teacher_id', $teacher->id)
            ->pluck('id');

        if ($classIds->isEmpty()) return false;

        return DB::table('group_members')
            ->where('user_id', $student->id)
            ->whereIn('group_id', $classIds)
            ->exists();
    }

    public function getAllowedContacts(User $me): array
    {
        if ($me->isTeacher()) {
            // Same-school teachers + director
            $colleagues = User::where('school_id', $me->school_id)
                ->where('id', '!=', $me->id)
                ->whereIn('role', ['teacher', 'director'])
                ->orderBy('role')
                ->orderBy('name')
                ->get();

            // Students in teacher's classes, grouped by class
            $classes = Group::where('school_id', $me->school_id)
                ->where('kind', 'class')
                ->where('teacher_id', $me->id)
                ->with(['members' => fn($q) => $q->where('users.role', 'student')->orderBy('users.name')])
                ->orderBy('name')
                ->get();

            return ['colleagues' => $colleagues, 'classes' => $classes];
        }

        if ($me->isDirector()) {
            $teachers = User::where('school_id', $me->school_id)
                ->where('role', 'teacher')->orderBy('name')->get();
            $students = User::where('school_id', $me->school_id)
                ->where('role', 'student')->orderBy('name')->get();
            return ['teachers' => $teachers, 'students' => $students];
        }

        // Student: all same-school users
        $others = User::where('school_id', $me->school_id)
            ->where('id', '!=', $me->id)
            ->whereIn('role', ['teacher', 'director', 'student'])
            ->orderBy('role')->orderBy('name')->get();
        return ['others' => $others];
    }
}
