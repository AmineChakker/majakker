<?php
namespace Database\Seeders;

use App\Models\{School, User, Filiere, Group, Post, Event, Badge, Hashtag};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class EsrmiSeeder extends Seeder
{
    private int $schoolId  = 189;
    private int $directorId = 542;

    public function run(): void
    {
        $school = School::find($this->schoolId);
        $school->update([
            'name'          => 'Lycée ESRMI',
            'city'          => 'Rabat',
            'initial'       => 'E',
            'plan'          => 'pro',
            'student_count' => 0, // will update after seeding
            'is_active'     => true,
        ]);

        // ── Filières ──────────────────────────────────────────────────────────
        $filieresData = [
            ['name' => 'Tronc Commun Sciences',               'code' => 'TC-S',  'level' => 'Tronc commun',  'color' => 'blue'],
            ['name' => 'Tronc Commun Lettres & Sc. Humaines', 'code' => 'TC-L',  'level' => 'Tronc commun',  'color' => 'atlas'],
            ['name' => 'Sciences Mathématiques',               'code' => 'SM',    'level' => '1ère Bac',      'color' => 'blue'],
            ['name' => 'Sciences de la Vie et de la Terre',   'code' => 'SVT',   'level' => '1ère Bac',      'color' => 'atlas'],
            ['name' => 'Sciences Économiques & Gestion',      'code' => 'SEG',   'level' => '1ère Bac',      'color' => 'saffron'],
            ['name' => 'Lettres & Sciences Humaines',         'code' => 'LSH',   'level' => '1ère Bac',      'color' => 'terracotta'],
            ['name' => 'Sciences Mathématiques',               'code' => 'SM',    'level' => '2ème Bac',      'color' => 'blue'],
            ['name' => 'Sciences Physiques & Chimie',         'code' => 'SPC',   'level' => '2ème Bac',      'color' => 'atlas'],
            ['name' => 'Sciences de la Vie et de la Terre',   'code' => 'SVT',   'level' => '2ème Bac',      'color' => 'atlas'],
            ['name' => 'Sciences Économiques & Gestion',      'code' => 'SEG',   'level' => '2ème Bac',      'color' => 'saffron'],
            ['name' => 'Lettres & Sciences Humaines',         'code' => 'LSH',   'level' => '2ème Bac',      'color' => 'terracotta'],
        ];
        $filieres = collect($filieresData)->map(fn($f) => Filiere::create(array_merge($f, [
            'school_id'   => $this->schoolId,
            'description' => null,
        ])));

        // ── Teachers ─────────────────────────────────────────────────────────
        $teachersData = [
            ['name' => 'Mohammed Benhaddou',   'email' => 'm.benhaddou@esrmi.ma',   'subject' => 'Mathématiques'],
            ['name' => 'Rachida El Khalfi',    'email' => 'r.elkhalfi@esrmi.ma',    'subject' => 'Physique-Chimie'],
            ['name' => 'Hassan Oulhaj',        'email' => 'h.oulhaj@esrmi.ma',      'subject' => 'Sciences de la Vie et de la Terre'],
            ['name' => 'Latifa Benchekroun',   'email' => 'l.benchekroun@esrmi.ma', 'subject' => 'Philosophie & Langue Arabe'],
            ['name' => 'Youssef El Ouardi',    'email' => 'y.elouardi@esrmi.ma',    'subject' => 'Langue Française'],
            ['name' => 'Souad Hssaisoune',     'email' => 's.hssaisoune@esrmi.ma',  'subject' => 'Histoire-Géographie'],
            ['name' => 'Abderrahman Mekouar',  'email' => 'a.mekouar@esrmi.ma',     'subject' => 'Sciences Économiques & Sociales'],
            ['name' => 'Nadia Amrani',         'email' => 'n.amrani@esrmi.ma',      'subject' => 'Langue Anglaise'],
            ['name' => 'Omar Chafii',          'email' => 'o.chafii@esrmi.ma',      'subject' => 'Éducation Physique & Sportive'],
            ['name' => 'Khadija Rharbaoui',    'email' => 'k.rharbaoui@esrmi.ma',   'subject' => 'Informatique & Technologies'],
        ];
        $teachers = collect($teachersData)->map(fn($t) => User::create([
            'name'      => $t['name'],
            'email'     => $t['email'],
            'password'  => Hash::make('password'),
            'role'      => 'teacher',
            'school_id' => $this->schoolId,
            'bio'       => 'Professeur de '.$t['subject'],
            'is_active' => true,
            'joined_at' => now()->subMonths(rand(12, 48)),
            'location'  => 'Rabat',
        ]));
        $t = $teachers; // shorthand

        // ── Classes with teacher assignments ──────────────────────────────────
        // [filière_index (0-based), section, teacher_index, color]
        $classesData = [
            // Tronc commun Sciences
            ['filiere'=>0, 'name'=>'TC Sciences · A', 'teacher'=>0, 'color'=>'blue'],
            ['filiere'=>0, 'name'=>'TC Sciences · B', 'teacher'=>1, 'color'=>'blue'],
            // Tronc commun Lettres
            ['filiere'=>1, 'name'=>'TC Lettres · A',  'teacher'=>4, 'color'=>'atlas'],
            // 1ère Bac SM
            ['filiere'=>2, 'name'=>'1ère Bac SM · A', 'teacher'=>0, 'color'=>'blue'],
            ['filiere'=>2, 'name'=>'1ère Bac SM · B', 'teacher'=>1, 'color'=>'blue'],
            // 1ère Bac SVT
            ['filiere'=>3, 'name'=>'1ère Bac SVT · A','teacher'=>2, 'color'=>'atlas'],
            // 1ère Bac SEG
            ['filiere'=>4, 'name'=>'1ère Bac SEG · A','teacher'=>6, 'color'=>'saffron'],
            // 1ère Bac LSH
            ['filiere'=>5, 'name'=>'1ère Bac LSH · A','teacher'=>3, 'color'=>'terracotta'],
            // 2ème Bac SM
            ['filiere'=>6, 'name'=>'2ème Bac SM · A', 'teacher'=>0, 'color'=>'blue'],
            ['filiere'=>6, 'name'=>'2ème Bac SM · B', 'teacher'=>1, 'color'=>'blue'],
            // 2ème Bac SPC
            ['filiere'=>7, 'name'=>'2ème Bac SPC · A','teacher'=>1, 'color'=>'atlas'],
            // 2ème Bac SVT
            ['filiere'=>8, 'name'=>'2ème Bac SVT · A','teacher'=>2, 'color'=>'atlas'],
            // 2ème Bac SEG
            ['filiere'=>9, 'name'=>'2ème Bac SEG · A','teacher'=>6, 'color'=>'saffron'],
            // 2ème Bac LSH
            ['filiere'=>10,'name'=>'2ème Bac LSH · A','teacher'=>3, 'color'=>'terracotta'],
        ];
        $classes = collect($classesData)->map(fn($c) => Group::create([
            'school_id'     => $this->schoolId,
            'filiere_id'    => $filieres[$c['filiere']]->id,
            'name'          => $c['name'],
            'kind'          => 'class',
            'color'         => $c['color'],
            'teacher_id'    => $t[$c['teacher']]->id,
            'member_count'  => 0,
            'academic_year' => '2025-2026',
            'capacity'      => 35,
        ]));

        // ── Assign teachers to their classes ─────────────────────────────────
        foreach ($classesData as $i => $cd) {
            $teacher = $t[$cd['teacher']];
            $class   = $classes[$i];
            $class->members()->syncWithoutDetaching([$teacher->id => ['role' => 'member']]);
        }

        // ── Students ─────────────────────────────────────────────────────────
        $firstNames = [
            'f' => ['Fatima','Nadia','Zineb','Hajar','Imane','Salma','Meryem','Ghita','Rania','Aya','Lina','Sara','Houda','Chaimae','Btissame','Manal','Asmaa','Lamia','Khadija','Loubna','Soukayna','Hafsa','Nisrine','Wafae','Ikram'],
            'm' => ['Mohammed','Youssef','Hamza','Amine','Mehdi','Omar','Karim','Ibrahim','Soufiane','Bilal','Ayoub','Othmane','Hicham','Khalid','Zakaria','Idriss','Noureddine','Saad','Tarik','Achraf','Badr','Ilyas','Anas','Reda','Walid'],
        ];
        $lastNames = ['El Amrani','Benali','Cherkaoui','Ouazzani','Tazi','Berrada','Alami','Lahlou','Bensouda','Chraibi','Mansouri','Bakkali','Rharbaoui','Filali','Bensalah','Naciri','Idrissi','Hassani','Bargach','Belkhayat','El Fassi','Kettani','Sefrioui','Benkirane','Zniber','El Khatib','Bouazza','Moussaoui','Raji','El Aaraj'];
        $usedEmails = [];
        $totalStudents = 0;

        // 25-30 students per class
        $studentsPerClass = [
            // TC Sciences: larger classes (30)
            30, 28,
            // TC Lettres
            25,
            // 1ère Bac
            28, 26, 24, 22, 20,
            // 2ème Bac
            26, 24, 22, 22, 20, 18,
        ];

        foreach ($classes as $i => $class) {
            $count = $studentsPerClass[$i] ?? 25;
            for ($j = 0; $j < $count; $j++) {
                $gender  = $j % 2 === 0 ? 'f' : 'm';
                $first   = $firstNames[$gender][array_rand($firstNames[$gender])];
                $last    = $lastNames[array_rand($lastNames)];
                $name    = $first.' '.$last;
                $baseEmail = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', str_replace([' ', "'"], ['.', ''], $name)))
                           . rand(1, 99) . '@esrmi.ma';
                // ensure unique
                $email = $baseEmail;
                $attempt = 0;
                while (in_array($email, $usedEmails) || User::where('email', $email)->exists()) {
                    $attempt++;
                    $email = strtolower(iconv('UTF-8', 'ASCII//TRANSLIT', str_replace([' ', "'"], ['.', ''], $first)))
                           . rand(10, 999) . '@esrmi.ma';
                }
                $usedEmails[] = $email;

                $student = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'password'  => Hash::make('password'),
                    'role'      => 'student',
                    'school_id' => $this->schoolId,
                    'is_active' => true,
                    'joined_at' => now()->subMonths(rand(1, 10)),
                    'location'  => 'Rabat',
                ]);
                $class->members()->syncWithoutDetaching([$student->id => ['role' => 'member']]);
                $totalStudents++;
            }
            $class->update(['member_count' => $count]);
        }

        $school->update(['student_count' => $totalStudents]);

        // ── Director clubs ────────────────────────────────────────────────────
        $clubs = [
            ['name' => 'Club Robotique & IA',       'color' => 'blue'],
            ['name' => 'Conseil des Élèves',         'color' => 'saffron'],
            ['name' => 'Club Débat & Prise de Parole','color' => 'atlas'],
            ['name' => 'Bibliothèque & Lecture',     'color' => 'terracotta'],
            ['name' => 'Club Environnement',         'color' => 'atlas'],
        ];
        foreach ($clubs as $club) {
            Group::create(array_merge($club, [
                'school_id'    => $this->schoolId,
                'kind'         => 'club',
                'member_count' => rand(12, 35),
            ]));
        }

        // ── Events ───────────────────────────────────────────────────────────
        $eventsData = [
            ['Journée Portes Ouvertes',          'Salle polyvalente ESRMI',  'blue',       now()->addDays(10)],
            ['Concours National de Mathématiques','Amphithéâtre',             'saffron',    now()->addDays(18)],
            ['Forum Orientation Bac+',           'Campus ESRMI',             'atlas',      now()->addDays(25)],
            ['Sortie pédagogique — Jardin d\'essais', 'Rabat',               'terracotta', now()->addDays(32)],
            ['Conseils de classe — 2ème trimestre','Salles de réunion',      'blue',       now()->addDays(5)],
            ['Compétition Robotique Régionale',  'Lycée Ibn Khaldoun, Salé', 'blue',       now()->addDays(45)],
            ['Fête de fin d\'année 2025-2026',   'ESRMI — Grande cour',     'saffron',    now()->addMonths(4)],
        ];
        foreach ($eventsData as [$title, $location, $color, $date]) {
            Event::create([
                'school_id'  => $this->schoolId,
                'created_by' => $this->directorId,
                'title'      => $title,
                'location'   => $location,
                'color'      => $color,
                'starts_at'  => $date,
            ]);
        }

        // ── Seed posts from director and teachers ─────────────────────────────
        $director = User::find($this->directorId);
        $postsData = [
            [$director, null,
             '📢 Chères familles, chers élèves,\n\nNous sommes heureux de vous accueillir pour l\'année scolaire 2025-2026 au Lycée ESRMI. Cette année s\'annonce riche en projets et en défis. Ensemble, nous viserons l\'excellence.',
             ['bienvenue','esrmi','rentrée2025'], true, true, 42, 8],
            [$director, null,
             'Les conseils de classe du 2ème trimestre se tiendront du 5 au 9 mai. Les bulletins seront disponibles sur l\'espace numérique sous 48h après chaque conseil.',
             ['conseil','bulletins'], true, true, 28, 5],
            [$t[0], $classes[3], // Maths teacher, 1ère Bac SM A
             'DM n°6 — Suites numériques et raisonnement par récurrence\n\nÀ rendre pour le vendredi 9 mai. Exercices prioritaires : 3, 4 et 6. Barème disponible sur l\'espace classe.',
             ['maths','dm','1bac'], false, false, 18, 12],
            [$t[1], $classes[0], // Physics teacher, TC Sciences A
             'Rappel : contrôle de Physique-Chimie jeudi 8 mai. Chapitres concernés : Électricité (1-3) et Mécanique newtonienne. Fiches de révision déposées dans l\'espace classe.',
             ['physique','contrôle','tcs'], false, false, 14, 7],
            [$t[2], $classes[5], // SVT teacher
             'Compte-rendu TP n°4 — Immunologie : à remettre avant le 12 mai. Consignes de rédaction dans l\'espace SVT. Les binômes non rendus auront un zéro.',
             ['svt','tp','immunologie'], false, false, 11, 4],
            [$t[4], null, // French teacher
             'Lecture conseillée pour les élèves du bac : "L\'Amour de loin" de Leïla Slimani. Un exemplaire est disponible à la bibliothèque. Résumé et analyse à la prochaine session.',
             ['lecture','français','bac'], false, false, 22, 9],
            [$t[6], $classes[6], // Economics teacher
             'Résultats du contrôle SEG — moyenne de classe : 13,4/20. Très bonne performance ! Correction détaillée projetée lundi en cours.',
             ['économie','résultats','seg'], false, false, 16, 6],
            [$director, null,
             'Nous félicitons nos élèves qui ont représenté ESRMI au Concours National de Sciences 2025 ! Mention spéciale à l\'équipe de 2ème Bac SM qui a décroché la 2ème place régionale. Bravo !',
             ['esrmi','fierté','concours'], true, true, 86, 14],
            [$t[0], $classes[8], // Maths, 2ème Bac SM A
             'Correction du bac blanc : les copies sont disponibles. RDV jeudi 8h pour la reprise collective. Focus sur l\'analyse complexe et la trigonométrie.',
             ['bacblanc','maths','2bac'], false, false, 19, 8],
            [$t[7], null, // English teacher
             'English Corner — chaque mercredi de 13h à 14h en salle 12. Tous niveaux bienvenus. Programme : conversation, actualités, préparation aux certifications.',
             ['english','esrmi','activité'], false, false, 31, 11],
        ];

        foreach ($postsData as [$author, $group, $body, $tags, $isPin, $isAnn, $likes, $comments]) {
            $post = Post::create([
                'user_id'         => $author->id,
                'school_id'       => $this->schoolId,
                'group_id'        => $group?->id,
                'body'            => $body,
                'is_pinned'       => $isPin,
                'is_announcement' => $isAnn,
                'visibility'      => $group ? 'group' : 'school',
                'likes_count'     => $likes,
                'comments_count'  => $comments,
                'sparks_count'    => rand(2, 15),
            ]);
            foreach ($tags as $tag) {
                $ht = Hashtag::firstOrCreate(['name' => $tag]);
                $ht->increment('posts_count');
                $post->hashtags()->syncWithoutDetaching([$ht->id]);
            }
        }

        // ── Badges for top students ───────────────────────────────────────────
        $topStudents = User::where('school_id', $this->schoolId)
            ->where('role', 'student')
            ->inRandomOrder()
            ->take(6)
            ->get();

        $badgePool = [
            ["1ère place Concours Maths 2025",    'saffron'],
            ["Mention Très Bien · 1er trimestre", 'blue'],
            ["Déléguée de classe 2025-2026",      'atlas'],
            ["Prix Meilleure Présentation ESRMI", 'terracotta'],
            ["100 jours de connexion",            'blue'],
            ["Champion débat inter-lycées",       'saffron'],
        ];

        foreach ($topStudents as $i => $student) {
            Badge::create([
                'user_id'    => $student->id,
                'label'      => $badgePool[$i][0],
                'color'      => $badgePool[$i][1],
                'awarded_at' => now()->subMonths(rand(1, 6)),
            ]);
        }

        // ── Summary ───────────────────────────────────────────────────────────
        $this->command->info('✓ ESRMI seeded:');
        $this->command->info('  Director : Fatimazahra Zniber — f.zniber@esrmi.ma / password');
        $this->command->info('  Filières : '.count($filieresData));
        $this->command->info('  Classes  : '.$classes->count());
        $this->command->info('  Teachers : '.$teachers->count().' → password: password');
        $this->command->info('  Students : '.$totalStudents.' → password: password');
        $this->command->info('  Events   : '.count($eventsData));
        $this->command->info('  Posts    : '.count($postsData));
    }
}
