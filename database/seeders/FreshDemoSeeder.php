<?php
namespace Database\Seeders;

use App\Models\{
    User, School, Group, Post, PostAttachment, Poll, PollOption,
    Event, Message, Badge, Hashtag, Filiere, Reaction, Comment
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};

class FreshDemoSeeder extends Seeder
{
    // ── Moroccan name pools ──────────────────────────────────────────────────
    private array $femaleFirst = [
        'Fatima','Nadia','Zineb','Hajar','Imane','Salma','Meryem','Ghita','Rania','Aya',
        'Lina','Sara','Houda','Chaimae','Btissame','Manal','Asmaa','Lamia','Khadija','Loubna',
        'Soukayna','Hafsa','Nisrine','Wafae','Ikram','Sanae','Mouna','Rajae','Ilham','Naima',
    ];
    private array $maleFirst = [
        'Mohammed','Youssef','Hamza','Amine','Mehdi','Omar','Karim','Ibrahim','Soufiane','Bilal',
        'Ayoub','Othmane','Hicham','Khalid','Zakaria','Idriss','Noureddine','Saad','Tarik','Achraf',
        'Badr','Ilyas','Anas','Reda','Walid','Sami','Adam','Nassim','Ryad','Taha',
    ];
    private array $lastNames = [
        'El Amrani','Benali','Cherkaoui','Ouazzani','Tazi','Berrada','Alami','Lahlou',
        'Bensouda','Chraibi','Mansouri','Bakkali','Rharbaoui','Filali','Bensalah','Naciri',
        'Idrissi','Hassani','Bargach','Belkhayat','El Fassi','Kettani','Sefrioui','Benkirane',
        'Zniber','El Khatib','Bouazza','Moussaoui','Raji','El Aaraj','Benhaddou','Mekouar',
        'Benchekroun','El Ouardi','Hssaisoune','Amrani','Chafii','Oulhaj','Tahiri','Boukhris',
    ];
    private array $usedEmails = [];

    public function run(): void
    {
        $this->command->info('🧹 Clearing all data (keeping admin)…');
        $this->clearData();

        $this->command->info('🏫 Creating 4 schools with full data…');

        $s1 = $this->buildSchool([
            'name' => 'Lycée Al Kindi', 'city' => 'Casablanca', 'initial' => 'K',
            'plan' => 'pro', 'student_count' => 0, 'slug' => 'alkindi',
        ]);
        $s2 = $this->buildSchool([
            'name' => 'Collège Ibn Rushd', 'city' => 'Rabat', 'initial' => 'R',
            'plan' => 'school', 'student_count' => 0, 'slug' => 'ibnrushd',
        ]);
        $s3 = $this->buildSchool([
            'name' => 'Lycée Averroès', 'city' => 'Marrakech', 'initial' => 'A',
            'plan' => 'pro', 'student_count' => 0, 'slug' => 'averroes',
        ]);
        $s4 = $this->buildSchool([
            'name' => 'Institut Atlas', 'city' => 'Tanger', 'initial' => 'T',
            'plan' => 'starter', 'student_count' => 0, 'slug' => 'atlas',
        ]);

        // ── School 1: Lycée Al Kindi, Casablanca (flagship) ─────────────────
        $this->command->info('  → Lycée Al Kindi (Casablanca)');
        $dir1 = $this->createDirector($s1, 'Samira El Fassi', 'director@majakker.ma');

        $teachers1 = $this->createTeachers($s1, [
            ['Mohammed Benhaddou',  'Mathématiques',          'teacher@majakker.ma'],
            ['Rachida El Khalfi',   'Physique-Chimie',        null],
            ['Hassan Oulhaj',       'SVT',                    null],
            ['Latifa Benchekroun', 'Philosophie & Arabe',    null],
            ['Youssef El Ouardi',  'Langue Française',       null],
            ['Souad Hssaisoune',   'Histoire-Géographie',    null],
            ['Abderrahman Mekouar','Économie & Gestion',     null],
            ['Nadia Amrani',       'Langue Anglaise',        null],
        ]);

        $filieres1 = $this->createFilieres($s1, [
            ['Sciences Mathématiques',             'SM',  '1ère Bac', 'blue'],
            ['Sciences de la Vie et de la Terre',  'SVT', '1ère Bac', 'atlas'],
            ['Sciences Économiques & Gestion',     'SEG', '1ère Bac', 'saffron'],
            ['Sciences Mathématiques',             'SM',  '2ème Bac', 'blue'],
            ['Sciences Physiques & Chimie',        'SPC', '2ème Bac', 'atlas'],
        ]);

        $classes1 = $this->createClasses($s1, $filieres1, $teachers1, [
            ['1ère Bac SM · A',    0, $teachers1[0], 'blue',       30, '2025-2026'],
            ['1ère Bac SM · B',    0, $teachers1[1], 'blue',       28, '2025-2026'],
            ['1ère Bac SVT · A',   1, $teachers1[2], 'atlas',      26, '2025-2026'],
            ['1ère Bac SEG · A',   2, $teachers1[6], 'saffron',    24, '2025-2026'],
            ['2ème Bac SM · A',    3, $teachers1[0], 'blue',       25, '2025-2026'],
            ['2ème Bac SPC · A',   4, $teachers1[1], 'atlas',      22, '2025-2026'],
        ]);

        $students1 = $this->createStudents($s1, $classes1, 'student@majakker.ma');
        $clubs1    = $this->createClubs($s1, $teachers1);
        $this->createEvents($s1, $dir1);
        $posts1    = $this->createPosts($s1, $dir1, $teachers1, $students1, $classes1);
        $this->createMessages($s1, $dir1, $teachers1, $students1);
        $this->createBadges($students1);

        // ── School 2: Collège Ibn Rushd, Rabat ───────────────────────────────
        $this->command->info('  → Collège Ibn Rushd (Rabat)');
        $dir2 = $this->createDirector($s2, 'Mohammed Benali', null);

        $teachers2 = $this->createTeachers($s2, [
            ['Karim El Idrissi',   'Mathématiques',       null],
            ['Salma Cherkaoui',    'Physique-Chimie',     null],
            ['Omar Berrada',       'SVT',                 null],
            ['Hassan Lahlou',      'Langue Arabe',        null],
            ['Fatima Benali',      'Langue Française',    null],
            ['Meryem Tahiri',      'Histoire-Géographie', null],
        ]);

        $filieres2 = $this->createFilieres($s2, [
            ['Tronc Commun Sciences',             'TC-S', 'Tronc commun', 'blue'],
            ['Tronc Commun Lettres',              'TC-L', 'Tronc commun', 'terracotta'],
            ['Sciences Mathématiques',            'SM',   '1ère Bac',    'blue'],
        ]);

        $classes2 = $this->createClasses($s2, $filieres2, $teachers2, [
            ['TC Sciences · A',   0, $teachers2[0], 'blue',       32, '2025-2026'],
            ['TC Sciences · B',   0, $teachers2[1], 'blue',       30, '2025-2026'],
            ['TC Lettres · A',    1, $teachers2[3], 'terracotta', 28, '2025-2026'],
            ['1ère Bac SM · A',   2, $teachers2[0], 'blue',       26, '2025-2026'],
        ]);

        $students2 = $this->createStudents($s2, $classes2, null);
        $this->createClubs($s2, $teachers2);
        $this->createEvents($s2, $dir2);
        $this->createPosts($s2, $dir2, $teachers2, $students2, $classes2);
        $this->createMessages($s2, $dir2, $teachers2, $students2);

        // ── School 3: Lycée Averroès, Marrakech ──────────────────────────────
        $this->command->info('  → Lycée Averroès (Marrakech)');
        $dir3 = $this->createDirector($s3, 'Fatima Zahra Haddad', null);

        $teachers3 = $this->createTeachers($s3, [
            ['Nour Eddine Tahiri', 'Mathématiques',    null],
            ['Bouchra Filali',     'SVT',              null],
            ['Amine Bargach',      'Physique-Chimie',  null],
            ['Sanae Benkirane',    'Langue Française', null],
            ['Mehdi Belkhayat',    'Histoire-Géo',     null],
            ['Ilham Naciri',       'Anglais',          null],
            ['Walid Sefrioui',     'Économie',         null],
        ]);

        $filieres3 = $this->createFilieres($s3, [
            ['Sciences Mathématiques',            'SM',  '2ème Bac', 'blue'],
            ['Sciences de la Vie et de la Terre', 'SVT', '2ème Bac', 'atlas'],
            ['Lettres & Sciences Humaines',       'LSH', '2ème Bac', 'terracotta'],
        ]);

        $classes3 = $this->createClasses($s3, $filieres3, $teachers3, [
            ['2ème Bac SM · A',   0, $teachers3[0], 'blue',       28, '2025-2026'],
            ['2ème Bac SM · B',   0, $teachers3[2], 'blue',       26, '2025-2026'],
            ['2ème Bac SVT · A',  1, $teachers3[1], 'atlas',      24, '2025-2026'],
            ['2ème Bac LSH · A',  2, $teachers3[3], 'terracotta', 22, '2025-2026'],
        ]);

        $students3 = $this->createStudents($s3, $classes3, null);
        $this->createClubs($s3, $teachers3);
        $this->createEvents($s3, $dir3);
        $this->createPosts($s3, $dir3, $teachers3, $students3, $classes3);
        $this->createMessages($s3, $dir3, $teachers3, $students3);

        // ── School 4: Institut Atlas, Tanger ─────────────────────────────────
        $this->command->info('  → Institut Atlas (Tanger)');
        $dir4 = $this->createDirector($s4, 'Youssef Chraibi', null);

        $teachers4 = $this->createTeachers($s4, [
            ['Laila Moussaoui',  'Mathématiques',    null],
            ['Hamza El Khatib',  'Physique-Chimie',  null],
            ['Rim Bouazza',      'SVT',              null],
            ['Tarik Raji',       'Langue Française', null],
        ]);

        $filieres4 = $this->createFilieres($s4, [
            ['Tronc Commun Sciences',  'TC-S', 'Tronc commun', 'blue'],
            ['Sciences Mathématiques', 'SM',   '1ère Bac',    'saffron'],
        ]);

        $classes4 = $this->createClasses($s4, $filieres4, $teachers4, [
            ['TC Sciences · A',  0, $teachers4[0], 'blue',    28, '2025-2026'],
            ['1ère Bac SM · A',  1, $teachers4[0], 'saffron', 22, '2025-2026'],
        ]);

        $students4 = $this->createStudents($s4, $classes4, null);
        $this->createClubs($s4, $teachers4);
        $this->createEvents($s4, $dir4);
        $this->createPosts($s4, $dir4, $teachers4, $students4, $classes4);
        $this->createMessages($s4, $dir4, $teachers4, $students4);

        // ── Summary ──────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('✅ Demo data seeded successfully!');
        $this->command->info('');
        $this->command->table(['Role','Email','Password','School'], [
            ['Admin',     'admin@majakker.ma',    'password', '—'],
            ['Directrice','director@majakker.ma', 'password', 'Lycée Al Kindi · Casablanca'],
            ['Enseignant','teacher@majakker.ma',  'password', 'Lycée Al Kindi · Casablanca'],
            ['Élève',     'student@majakker.ma',  'password', 'Lycée Al Kindi · Casablanca'],
        ]);
        $this->command->info('  Users:    '.User::count());
        $this->command->info('  Schools:  '.School::count());
        $this->command->info('  Classes:  '.Group::where('kind','class')->count());
        $this->command->info('  Posts:    '.Post::count());
        $this->command->info('  Messages: '.Message::count());
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function clearData(): void
    {
        $adminId = User::where('role','admin')->value('id');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('poll_votes')->truncate();
        DB::table('poll_options')->truncate();
        DB::table('polls')->truncate();
        DB::table('post_hashtag')->truncate();
        DB::table('hashtags')->truncate();
        DB::table('post_attachments')->truncate();
        DB::table('reactions')->truncate();
        DB::table('comments')->truncate();
        DB::table('posts')->truncate();
        DB::table('messages')->truncate();
        DB::table('notifications')->truncate();
        DB::table('badges')->truncate();
        DB::table('group_members')->truncate();
        DB::table('groups')->truncate();
        DB::table('filieres')->truncate();
        DB::table('events')->truncate();
        DB::table('moderation_reports')->truncate();
        DB::table('users')->where('id', '!=', $adminId)->delete();
        DB::table('schools')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Reset used email list (keep admin email)
        $this->usedEmails = ['admin@majakker.ma'];
    }

    private function buildSchool(array $data): School
    {
        return School::create([
            'name'          => $data['name'],
            'city'          => $data['city'],
            'initial'       => $data['initial'],
            'plan'          => $data['plan'],
            'student_count' => 0,
            'is_active'     => true,
            'joined_at'     => now()->subMonths(rand(6, 24)),
        ]);
    }

    private function createDirector(School $school, string $name, ?string $forcedEmail): User
    {
        $email = $forcedEmail ?? $this->genEmail($name, $school);
        $user  = User::create([
            'name'      => $name,
            'email'     => $email,
            'password'  => Hash::make('password'),
            'role'      => 'director',
            'school_id' => $school->id,
            'bio'       => 'Directeur(trice) de '.$school->name.' depuis '.now()->subYears(rand(2,6))->year.'.',
            'location'  => $school->city,
            'is_active' => true,
            'joined_at' => now()->subMonths(rand(24, 48)),
        ]);
        $this->usedEmails[] = $email;
        return $user;
    }

    private function createTeachers(School $school, array $data): array
    {
        return array_map(function ($row) use ($school) {
            [$name, $subject, $forcedEmail] = $row;
            $email = $forcedEmail ?? $this->genEmail($name, $school);
            $user  = User::create([
                'name'      => $name,
                'email'     => $email,
                'password'  => Hash::make('password'),
                'role'      => 'teacher',
                'school_id' => $school->id,
                'bio'       => 'Professeur de '.$subject.' — '.$school->name.'.',
                'location'  => $school->city,
                'is_active' => true,
                'joined_at' => now()->subMonths(rand(12, 48)),
            ]);
            $this->usedEmails[] = $email;
            return $user;
        }, $data);
    }

    private function createFilieres(School $school, array $data): array
    {
        return array_map(fn($f) => Filiere::create([
            'school_id' => $school->id,
            'name'      => $f[0],
            'code'      => $f[1],
            'level'     => $f[2],
            'color'     => $f[3],
        ]), $data);
    }

    private function createClasses(School $school, array $filieres, array $teachers, array $data): array
    {
        return array_map(function ($row) use ($school, $filieres, $teachers) {
            [$name, $filIdx, $teacher, $color, $cap, $year] = $row;
            return Group::create([
                'school_id'     => $school->id,
                'filiere_id'    => $filieres[$filIdx]->id,
                'name'          => $name,
                'kind'          => 'class',
                'color'         => $color,
                'teacher_id'    => $teacher->id,
                'member_count'  => 0,
                'academic_year' => $year,
                'capacity'      => $cap,
            ]);
        }, $data);
    }

    private function createStudents(School $school, array $classes, ?string $firstEmail): array
    {
        $students = [];
        $perClass = [30, 28, 26, 24, 22, 20, 18, 16];

        foreach ($classes as $i => $class) {
            $count = $perClass[$i] ?? 20;
            for ($j = 0; $j < $count; $j++) {
                $gender = $j % 2 === 0 ? 'f' : 'm';
                $name   = $this->randomName($gender);
                $forced = ($i === 0 && $j === 0) ? $firstEmail : null;
                $email  = $forced ?? $this->genEmail($name, $school);

                $student = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => Hash::make('password'),
                    'role'      => 'student',
                    'school_id' => $school->id,
                    'is_active' => true,
                    'joined_at' => now()->subMonths(rand(1, 12)),
                    'location'  => $school->city,
                ]);
                $this->usedEmails[] = $email;

                $class->members()->syncWithoutDetaching([$student->id => ['role' => 'member']]);
                $students[] = $student;
            }
            $class->update(['member_count' => $count]);
        }

        // Update school student count
        $school->update(['student_count' => count($students)]);

        return $students;
    }

    private function createClubs(School $school, array $teachers): array
    {
        $clubDefs = [
            ['Club Robotique & IA',         'blue'],
            ['Conseil des Élèves',          'saffron'],
            ['Club Débat',                  'atlas'],
            ['Bibliothèque & Culture',      'terracotta'],
            ['Club Environnement',          'atlas'],
        ];
        $clubs = [];
        foreach (array_slice($clubDefs, 0, rand(2, 4)) as $def) {
            $clubs[] = Group::create([
                'school_id'    => $school->id,
                'name'         => $def[0],
                'kind'         => 'club',
                'color'        => $def[1],
                'teacher_id'   => $teachers[array_rand($teachers)]->id,
                'member_count' => rand(12, 35),
            ]);
        }
        return $clubs;
    }

    private function createEvents(School $school, User $director): void
    {
        $events = [
            ['Journée Portes Ouvertes',           'Grande salle',         'blue',       now()->addDays(8)],
            ['Conseil de classe — 2ème trimestre', 'Salles de réunion',   'saffron',    now()->addDays(3)],
            ['Concours de Mathématiques',          'Amphithéâtre',        'atlas',      now()->addDays(14)],
            ['Sortie pédagogique',                 $school->city,         'terracotta', now()->addDays(21)],
            ['Fête de fin d\'année 2025-2026',     $school->name,         'saffron',    now()->addMonths(3)],
        ];
        foreach (array_slice($events, 0, rand(3, 5)) as [$title,$loc,$color,$date]) {
            Event::create([
                'school_id'  => $school->id,
                'created_by' => $director->id,
                'title'      => $title,
                'location'   => $loc,
                'color'      => $color,
                'starts_at'  => $date,
            ]);
        }
    }

    private function createPosts(School $school, User $director, array $teachers, array $students, array $classes): array
    {
        $posts = [];

        // Director announcements
        $announcements = [
            [
                'title' => 'Rentrée scolaire 2025-2026 — Bienvenue !',
                'body'  => "Chères familles, chers élèves et chers professeurs,\n\nC'est avec un immense plaisir que nous vous accueillons pour cette nouvelle année scolaire à ".$school->name.". Cette année s'annonce riche en projets, en défis et en réussites. Ensemble, nous visons l'excellence.\n\nBonne rentrée à toutes et à tous !",
                'tags'  => ['rentrée2025', $this->slugify($school->name), 'bienvenue'],
                'likes' => rand(45, 120), 'sparks' => rand(10, 30), 'comments' => rand(8, 20),
                'pinned' => true,
            ],
            [
                'title' => 'Résultats du 1er trimestre — Félicitations !',
                'body'  => "Les résultats du premier trimestre sont disponibles sur l'espace numérique. La moyenne générale de l'établissement est en hausse de 0,8 point par rapport à l'année dernière. Félicitations à tous les élèves et un grand merci aux enseignants pour leur engagement.",
                'tags'  => ['résultats', 'félicitations', $this->slugify($school->name)],
                'likes' => rand(38, 95), 'sparks' => rand(8, 22), 'comments' => rand(5, 15),
                'pinned' => false,
            ],
            [
                'body' => "Rappel important : les demandes d'attestations de scolarité sont à déposer au secrétariat avant le vendredi 15 mai. Merci de vous munir de votre carte d'étudiant.",
                'tags' => ['administratif', 'attestation'],
                'likes' => rand(12, 40), 'sparks' => rand(3, 12), 'comments' => rand(2, 8),
                'pinned' => false,
            ],
        ];

        foreach ($announcements as $data) {
            $post = $this->makePost($director, $school, null, $data, true);
            if ($data['pinned'] ?? false) $post->update(['is_pinned' => true]);
            $posts[] = $post;
        }

        // Teacher posts per class
        $teacherPostTemplates = [
            [
                'title' => 'DM n°{{n}} — {{subject}}',
                'body'  => "Voici le devoir maison n°{{n}} à rendre pour le {{date}}.\n\nExercices prioritaires : {{exo}}. La correction sera projetée en classe la semaine suivante. Bon courage !",
                'tags'  => ['devoir', '{{slug}}', 'révision'],
            ],
            [
                'body' => "Rappel : contrôle de {{subject}} {{day}}. Chapitres concernés : {{chapters}}. Les fiches de révision sont disponibles dans l'espace classe.",
                'tags' => ['contrôle', '{{slug}}'],
            ],
            [
                'body' => "Bonjour à tous ! Voici le compte-rendu du TP de {{subject}} n°{{n}}. Merci de corriger vos erreurs avant la prochaine séance. N'hésitez pas à poser vos questions en commentaire.",
                'tags' => ['tp', '{{slug}}', 'sciences'],
            ],
            [
                'title' => 'Ressources — Chapitre {{n}}',
                'body'  => "Je partage ici les ressources complémentaires pour le chapitre {{n}}. Ces documents vous aideront à approfondir les notions vues en cours. Bonne lecture !",
                'tags'  => ['ressources', 'cours', '{{slug}}'],
            ],
        ];

        $subjects = ['mathématiques', 'physique', 'svt', 'français', 'anglais', 'histoire', 'économie'];
        $chaptersList = [
            'Fonctions dérivées et primitives',
            'Électricité — Régimes transitoires',
            'Immunologie et réponse immunitaire',
            'Littérature du XXe siècle',
            'Narration et temps grammaticaux',
        ];

        foreach ($classes as $class) {
            $teacher = User::find($class->teacher_id) ?? ($teachers[0] ?? null);
            if (!$teacher) continue;

            $numPosts = rand(2, 4);
            for ($i = 0; $i < $numPosts; $i++) {
                $tpl  = $teacherPostTemplates[$i % count($teacherPostTemplates)];
                $subj = $subjects[array_rand($subjects)];
                $n    = rand(3, 8);
                $days = [2, 3, 4, 5, 6, 7, 8, 9, 10];
                $day  = 'lundi '.date('d M', strtotime("+{$days[$i % count($days)]} days"));

                $body = strtr($tpl['body'] ?? '', [
                    '{{subject}}' => $subj, '{{n}}' => $n, '{{day}}' => $day,
                    '{{date}}'    => $day,
                    '{{exo}}'     => ''.rand(1,4).' et '.rand(5,8),
                    '{{chapters}}'=> $chaptersList[array_rand($chaptersList)],
                ]);
                $title = isset($tpl['title']) ? strtr($tpl['title'], ['{{n}}'=>$n,'{{subject}}'=>ucfirst($subj)]) : null;
                $tags  = array_map(fn($t) => strtr($t, ['{{slug}}'=>$subj]), $tpl['tags']);

                $post = $this->makePost($teacher, $school, $class, [
                    'title' => $title, 'body' => $body, 'tags' => $tags,
                    'likes' => rand(8, 35), 'sparks' => rand(2, 10), 'comments' => rand(3, 12),
                ], false);
                $posts[] = $post;
            }
        }

        // Student posts
        $studentPostTemplates = [
            "On a fini de monter le projet pour le concours de {{city}} ! Il reste à peaufiner la présentation demain. Croisons les doigts 🤞",
            "Est-ce que quelqu'un a la fiche de révision sur {{topic}} ? Je l'ai cherchée partout sur le drive…",
            "Super séance de TP aujourd'hui ! On a enfin compris le principe de {{concept}}. Merci à notre prof pour les explications claires.",
            "Rappel pour les membres du club : réunion {{day}} à {{hour}}h en salle {{room}}. On vote pour le thème du prochain événement !",
            "J'ai trouvé cette vidéo super pour réviser {{topic}}. Je la partage pour ceux qui préparent le contrôle 👇",
            "Quelqu'un a déjà passé le bac blanc de {{subject}} cette année ? Comment vous trouvez le niveau des sujets ?",
        ];
        $topics    = ['la thermodynamique','l\'immunologie','les fonctions','la littérature engagée','la macroéconomie'];
        $concepts  = ['la résonance','la mitose','l\'intégration','la cohérence textuelle'];
        $hours     = [14, 15, 16, 17];
        $rooms     = [12, 14, 15, 'B3', 'A2'];
        $days      = ['mercredi','jeudi','vendredi'];

        foreach (array_slice($students, 0, min(count($students), rand(15, 25))) as $j => $student) {
            $tpl  = $studentPostTemplates[$j % count($studentPostTemplates)];
            $body = strtr($tpl, [
                '{{city}}'   => $school->city,
                '{{topic}}'  => $topics[array_rand($topics)],
                '{{concept}}' => $concepts[array_rand($concepts)],
                '{{subject}}' => $subjects[array_rand($subjects)],
                '{{day}}'    => $days[array_rand($days)],
                '{{hour}}'   => $hours[array_rand($hours)],
                '{{room}}'   => $rooms[array_rand($rooms)],
            ]);
            $tags = [
                $this->slugify($school->name),
                $subjects[array_rand($subjects)],
                'élèves',
            ];
            $post = $this->makePost($student, $school, null, [
                'body'    => $body,
                'tags'    => $tags,
                'likes'   => rand(3, 28),
                'sparks'  => rand(1, 8),
                'comments'=> rand(1, 10),
            ], false);
            $posts[] = $post;
        }

        // Add poll to one teacher post
        if (count($posts) > 3) {
            $pollPost = $this->makePost($teachers[0], $school, $classes[0] ?? null, [
                'body'    => "Sondage rapide avant le contrôle de vendredi : sur quel chapitre souhaitez-vous une révision en classe ?",
                'tags'    => ['sondage', 'révision'],
                'likes'   => rand(15, 45), 'sparks' => rand(3, 12), 'comments' => rand(4, 10),
            ], false);
            $att  = PostAttachment::create(['post_id' => $pollPost->id, 'kind' => 'poll']);
            $poll = Poll::create(['attachment_id' => $att->id, 'question' => 'Quel chapitre réviser en priorité ?', 'ends_at' => now()->addDays(3)]);
            foreach (['Analyse numérique','Probabilités','Géométrie','Trigonométrie'] as $k => $opt) {
                PollOption::create(['poll_id' => $poll->id, 'label' => $opt, 'sort_order' => $k, 'votes_count' => rand(5, 25)]);
            }
        }

        // Add reactions and comments to posts
        $allUsers = array_merge([$director], $teachers, array_slice($students, 0, min(count($students), 20)));
        foreach ($posts as $post) {
            // Reactions
            $reactors = collect($allUsers)->shuffle()->take($post->likes_count > 0 ? min($post->likes_count, 8) : 0);
            foreach ($reactors as $reactor) {
                if ($reactor->id !== $post->user_id) {
                    Reaction::firstOrCreate(['user_id' => $reactor->id, 'post_id' => $post->id, 'type' => 'like']);
                }
            }
            // Comments
            $commenters = collect($allUsers)->shuffle()->take(min($post->comments_count, 4));
            $commentBodies = [
                'Merci pour ce partage ! Très utile.',
                'Super travail, bravo à toute l\'équipe.',
                'Est-ce que c\'est pour tous les niveaux ou juste le bac ?',
                'J\'ai une question sur le point 3, je passe en salle {{room}} ?',
                'Merci professeur, j\'ai tout noté.',
                'Très intéressant ! On peut avoir les ressources en PDF ?',
                'On peut travailler en groupe pour ce devoir ?',
                'Merci pour le rappel !',
            ];
            foreach ($commenters as $commenter) {
                $body = strtr($commentBodies[array_rand($commentBodies)], ['{{room}}' => $rooms[array_rand($rooms)]]);
                Comment::create([
                    'post_id'    => $post->id,
                    'user_id'    => $commenter->id,
                    'body'       => $body,
                    'created_at' => now()->subMinutes(rand(5, 1440)),
                    'updated_at' => now()->subMinutes(rand(1, 60)),
                ]);
            }
        }

        return $posts;
    }

    private function makePost(User $author, School $school, ?Group $group, array $data, bool $isAnn): Post
    {
        $post = Post::create([
            'user_id'         => $author->id,
            'school_id'       => $school->id,
            'group_id'        => $group?->id,
            'title'           => $data['title'] ?? null,
            'body'            => $data['body'] ?? '',
            'is_announcement' => $isAnn,
            'is_pinned'       => false,
            'visibility'      => $group ? 'group' : 'school',
            'likes_count'     => $data['likes'] ?? 0,
            'sparks_count'    => $data['sparks'] ?? 0,
            'comments_count'  => $data['comments'] ?? 0,
            'created_at'      => now()->subHours(rand(1, 168)),
            'updated_at'      => now()->subHours(rand(0, 24)),
        ]);

        foreach ($data['tags'] ?? [] as $tagName) {
            if (!$tagName) continue;
            $tag = Hashtag::firstOrCreate(['name' => strtolower($tagName)]);
            $tag->increment('posts_count');
            $post->hashtags()->syncWithoutDetaching([$tag->id]);
        }
        return $post;
    }

    private function createMessages(School $school, User $director, array $teachers, array $students): void
    {
        $conversations = [
            // Director ↔ Teacher 1
            [$director, $teachers[0], [
                ['Bonjour, pouvez-vous me transmettre le bilan du 1er trimestre pour la classe SM A avant vendredi ?', 0],
                ['Bien sûr, je vous l\'envoie ce soir par e-mail. La moyenne est à 13,4, en hausse de 1,2 pts.', 1],
                ['Excellent ! On en parlera lors du conseil de classe. Merci.', 0],
                ['Entendu. À vendredi alors.', 1],
            ]],
            // Director ↔ Teacher 2
            [$director, $teachers[1 % count($teachers)], [
                ['Bonjour. Le calendrier des examens blancs est-il finalisé ?', 1],
                ['Oui, j\'ai transmis les dates au secrétariat. Les surveillances seront affichées demain.', 0],
                ['Parfait, merci pour la réactivité.', 1],
            ]],
            // Teacher 1 ↔ Student 1
            [$teachers[0], $students[0] ?? $teachers[0], [
                ['Bonjour ! J\'ai une question sur l\'exercice 3 du devoir. Je bloque sur la partie b).', 1],
                ['Bonjour ! Pour la partie b), il faut appliquer le théorème de la valeur intermédiaire. Reprenez bien la définition en cours.', 0],
                ['Ah oui ! Je vois maintenant. Merci beaucoup professeur.', 1],
                ['De rien. N\'hésitez pas si vous avez d\'autres questions.', 0],
            ]],
            // Teacher 1 ↔ Teacher 2
            [count($teachers) > 1 ? $teachers[0] : $director, count($teachers) > 1 ? $teachers[1] : $director, [
                ['Salut, tu as vu les résultats du sondage sur les révisions ? Les élèves veulent surtout revoir l\'analyse numérique.', 0],
                ['Oui j\'ai vu ! Je vais adapter mon cours de demain. Tu as des supports à partager ?', 1],
                ['Je t\'envoie ma fiche résumé tout à l\'heure.', 0],
                ['Super merci ! Tu veux qu\'on fasse une révision commune vendredi matin ?', 1],
                ['Bonne idée. Disons 8h en salle 12 ?', 0],
                ['Parfait, c\'est noté !', 1],
            ]],
            // Teacher ↔ Student 2
            [$teachers[0], $students[min(4, count($students)-1)] ?? $students[0], [
                ['Bonjour, j\'ai remarqué votre absence lors du dernier TP. Tout va bien ?', 0],
                ['Bonjour professeur, excusez-moi, j\'étais malade. Est-ce que je peux rattraper le TP ?', 1],
                ['Bien sûr, passez me voir mardi à la pause. Apportez votre cahier de TP.', 0],
                ['Merci beaucoup ! Je serai là à 10h.', 1],
            ]],
        ];

        foreach ($conversations as [$userA, $userB, $msgs]) {
            if ($userA->id === $userB->id) continue;
            $baseTime = now()->subHours(rand(1, 72));
            foreach ($msgs as $k => [$body, $whoSends]) {
                $sender    = $whoSends === 0 ? $userA : $userB;
                $recipient = $whoSends === 0 ? $userB : $userA;
                Message::create([
                    'sender_id'    => $sender->id,
                    'recipient_id' => $recipient->id,
                    'body'         => $body,
                    'read_at'      => $k < count($msgs) - 1 ? now()->subMinutes(rand(1, 60)) : null,
                    'created_at'   => $baseTime->copy()->addMinutes($k * rand(3, 30)),
                    'updated_at'   => $baseTime->copy()->addMinutes($k * rand(3, 30)),
                ]);
            }
        }
    }

    private function createBadges(array $students): void
    {
        $badges = [
            ['1ère place Concours National Maths', 'saffron'],
            ['Mention Très Bien · 1er trimestre',  'blue'],
            ['Déléguée de classe 2025-2026',        'atlas'],
            ['Prix Meilleure Présentation',         'terracotta'],
            ['100 jours de connexion',              'blue'],
            ['Champion du Débat inter-lycées',      'saffron'],
            ['Projet Robotique — Médaille d\'or',   'terracotta'],
        ];
        $honorees = array_slice($students, 0, min(count($students), 6));
        foreach ($honorees as $i => $student) {
            Badge::create([
                'user_id'    => $student->id,
                'label'      => $badges[$i % count($badges)][0],
                'color'      => $badges[$i % count($badges)][1],
                'awarded_at' => now()->subMonths(rand(1, 5)),
            ]);
        }
    }

    // ── Utility ──────────────────────────────────────────────────────────────

    private function genEmail(string $name, School $school): string
    {
        $slug    = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', str_replace([' ', "'", '-'], ['.', '', ''], $name)));
        $domain  = strtolower(preg_replace('/[^a-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $school->name))).'.ma';
        $base    = "{$slug}@{$domain}";
        $email   = $base;
        $attempt = 1;
        while (in_array($email, $this->usedEmails) || User::where('email', $email)->exists()) {
            $email = $slug.$attempt.'@'.$domain;
            $attempt++;
        }
        $this->usedEmails[] = $email;
        return $email;
    }

    private function randomName(string $gender): string
    {
        $first = $gender === 'f'
            ? $this->femaleFirst[array_rand($this->femaleFirst)]
            : $this->maleFirst[array_rand($this->maleFirst)];
        $last = $this->lastNames[array_rand($this->lastNames)];
        return $first.' '.$last;
    }

    private function slugify(string $s): string
    {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', iconv('UTF-8', 'ASCII//TRANSLIT', $s)));
    }
}
