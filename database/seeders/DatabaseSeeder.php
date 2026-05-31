<?php
namespace Database\Seeders;
use App\Models\{User,School,Group,Post,PostAttachment,Poll,PollOption,Event,Badge,Hashtag,Reaction,Comment,Message};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {

        // ── Schools ──────────────────────────────────────────────────────────
        $schools = [
            ['name'=>'Lycée Majakker','city'=>'Casablanca','initial'=>'M','student_count'=>1284,'plan'=>'pro'],
            ['name'=>'École Al Khawarizmi','city'=>'Rabat','initial'=>'A','student_count'=>856,'plan'=>'school'],
            ['name'=>'Lycée Lalla Salma','city'=>'Marrakech','initial'=>'L','student_count'=>1102,'plan'=>'school'],
            ['name'=>'Groupe Atlas','city'=>'Tanger','initial'=>'G','student_count'=>742,'plan'=>'starter'],
        ];
        $schoolModels = collect($schools)->map(fn($s)=>School::create(array_merge($s,['is_active'=>true,'joined_at'=>now()->subMonths(rand(6,24))])));
        $mainSchool = $schoolModels->first();

        // ── Admin ─────────────────────────────────────────────────────────────
        User::create(['name'=>'Reda Amrani','email'=>'admin@majakker.ma','password'=>Hash::make('password'),'role'=>'admin','school_id'=>null,'is_active'=>true,'joined_at'=>now()->subYear(),'bio'=>'Super admin EduSphere','location'=>'Rabat']);

        // ── Users per school ──────────────────────────────────────────────────
        foreach ($schoolModels as $school) {
            $dir = User::create(['name'=>'Najat Tazi','email'=>'director@'.$school->id.'.majakker.ma','password'=>Hash::make('password'),'role'=>'director','school_id'=>$school->id,'is_active'=>true,'joined_at'=>now()->subMonths(24),'bio'=>'Directrice depuis 2021.','location'=>$school->city]);
            if ($school->id === $mainSchool->id) {
                User::where('name','Najat Tazi')->where('school_id',$mainSchool->id)->update(['email'=>'director@majakker.ma']);
            }

            $teachers = collect(['Karim El Idrissi','Salma Cherkaoui','Omar Berrada','Hassan Lahlou','Fatima Benali','Meryem Tahiri','Youssef Alami','Nadia Boukhris'])->map(fn($name)=>
                User::create(['name'=>$name,'email'=>strtolower(str_replace(' ','.',$name)).'@'.$school->id.'.majakker.ma','password'=>Hash::make('password'),'role'=>'teacher','school_id'=>$school->id,'is_active'=>true,'joined_at'=>now()->subMonths(rand(6,24)),'location'=>$school->city])
            );
            if ($school->id === $mainSchool->id) {
                User::where('name','Karim El Idrissi')->where('school_id',$mainSchool->id)->update(['email'=>'teacher@majakker.ma']);
            }

            $students = collect(['Yasmine Bennani','Mehdi Ouazzani','Sara El Amrani','Hamza Tounsi','Loubna Raji','Adam Chraibi','Mariam Essaidi','Amine Khattabi','Zineb Moussaoui','Khalid Bouchikhi'])->map(fn($name)=>
                User::create(['name'=>$name,'email'=>strtolower(str_replace(' ','.',$name)).'@'.$school->id.'.majakker.ma','password'=>Hash::make('password'),'role'=>'student','school_id'=>$school->id,'is_active'=>true,'joined_at'=>now()->subMonths(rand(1,12)),'location'=>$school->city])
            );
            if ($school->id === $mainSchool->id) {
                User::where('name','Yasmine Bennani')->where('school_id',$mainSchool->id)->update(['email'=>'student@majakker.ma']);
            }

            $allUsers = collect([$dir, ...$teachers, ...$students]);

            // ── Groups ────────────────────────────────────────────────────────
            $classes = [
                ['name'=>'Tle BAC · Sciences math','kind'=>'class','color'=>'blue'],
                ['name'=>'1ère SVT','kind'=>'class','color'=>'atlas'],
                ['name'=>'2nde Bac · Lettres','kind'=>'class','color'=>'saffron'],
                ['name'=>'3ème AC · A','kind'=>'class','color'=>'terracotta'],
            ];
            $clubs = [
                ['name'=>'Club Robotique','kind'=>'club','color'=>'blue'],
                ['name'=>'Conseil des Élèves','kind'=>'club','color'=>'saffron'],
                ['name'=>'Bibliothèque','kind'=>'club','color'=>'atlas'],
            ];
            $groupModels = collect([...$classes,...$clubs])->map(fn($g)=>Group::create(array_merge($g,['school_id'=>$school->id,'teacher_id'=>$teachers->first()->id,'member_count'=>rand(15,40)])));

            $groupModels->each(function($grp) use($students,$teachers) {
                $members = $students->random(min(10,$students->count()))->push($teachers->first());
                $members->each(fn($u)=>$grp->members()->syncWithoutDetaching([$u->id=>['role'=>'member']]));
                $grp->update(['member_count'=>$members->count()]);
            });

            $allUsers = collect([$dir, ...$teachers, ...$students]);

            // ── Posts ─────────────────────────────────────────────────────────
            $seedPosts = [
                ['author'=>$dir,'title'=>'Semaine des sciences — du 18 au 22 mai','body'=>"Cher·e·s élèves, professeur·e·s,\n\nLa Semaine des sciences débute lundi avec une conférence de Pr. Hassan Aourag (UM6P) en grande salle. Inscriptions via le lien ci-dessous.",'is_pinned'=>true,'is_announcement'=>true,'tags'=>['sciences','majakker'],'likes'=>15,'comments'=>8,'sparks'=>6],
                ['author'=>$teachers->first(),'title'=>'DM 7 — Suites et raisonnement par récurrence','body'=>"Voici le DM à rendre pour vendredi 16 mai. Les exercices 3 et 5 sont prioritaires.",'is_announcement'=>true,'tags'=>['maths','2bac'],'likes'=>10,'comments'=>5,'sparks'=>3],
                ['author'=>$students->first(),'body'=>"On a fini de souder le bras articulé pour le concours d'Ifrane ! Reste à câbler les servos demain.",'tags'=>['robotique','concours'],'likes'=>8,'comments'=>4,'sparks'=>5],
                ['author'=>$teachers->get(1),'body'=>"Petit sondage avant le contrôle de jeudi : sur quel chapitre voulez-vous une révision en classe ?",'tags'=>['svt'],'likes'=>5,'comments'=>3,'sparks'=>1,'poll'=>true,'poll_q'=>'Sur quel chapitre faut-il revenir ?','poll_opts'=>['Génétique des populations','Immunologie','Respiration cellulaire']],
                ['author'=>$students->get(1),'body'=>"Quelqu'un a la fiche de M. Berrada sur Bergson ? Je l'ai cherchée partout sur le drive.",'tags'=>['philo','tbac'],'likes'=>4,'comments'=>7,'sparks'=>0],
                ['author'=>$students->get(2),'title'=>'Bal de fin d\'année — vote du thème','body'=>"Le conseil des élèves ouvre le vote pour le thème du bal du 28 juin.",'tags'=>['bal2026','majakker'],'likes'=>12,'comments'=>6,'sparks'=>5,'poll'=>true,'poll_q'=>'Quel thème pour le bal ?','poll_opts'=>['Hollywood glamour','Nuit étoilée','Années 80 rétro','Masquerade']],
                ['author'=>$dir,'body'=>"Nouveau dans nos rayons : « Les Fondations » d'Asimov en édition intégrale.",'tags'=>['bibliothèque'],'likes'=>9,'comments'=>3,'sparks'=>8],
            ];

            // ── Group-specific posts ──────────────────────────────────────────
            $robotClub = $groupModels->firstWhere('name','Club Robotique');
            if ($robotClub) {
                $seedPosts[] = ['author'=>$students->first(),'group_id'=>$robotClub->id,'title'=>'Code du robot — PR à review','body'=>"J'ai poussé le code du bras articulé sur le repo GitHub. Quelqu'un peut review la PR #42 ?",'tags'=>['robotique','code'],'likes'=>6,'comments'=>4,'sparks'=>3];
            }
            $studentCouncil = $groupModels->firstWhere('name','Conseil des Élèves');
            if ($studentCouncil) {
                $seedPosts[] = ['author'=>$students->get(2),'group_id'=>$studentCouncil->id,'title'=>'Ordre du jour — réunion du 3 juin','body'=>"Points à discuter :\n1. Préparation du bal\n2. Budget sorties scolaires\n3. Nouveaux membres",'tags'=>['conseil'],'likes'=>7,'comments'=>5,'sparks'=>2];
            }

            foreach ($seedPosts as $pd) {
                $post = Post::create([
                    'user_id'        => $pd['author']->id,
                    'school_id'      => $school->id,
                    'group_id'       => $pd['group_id'] ?? null,
                    'title'          => $pd['title'] ?? null,
                    'body'           => $pd['body'],
                    'is_pinned'      => $pd['is_pinned'] ?? false,
                    'is_announcement'=> $pd['is_announcement'] ?? false,
                    'visibility'     => ($pd['group_id'] ?? false) ? 'group' : 'school',
                    'likes_count'    => 0,
                    'comments_count' => 0,
                    'sparks_count'   => 0,
                ]);
                foreach ($pd['tags'] ?? [] as $tag) {
                    $ht = Hashtag::firstOrCreate(['name'=>$tag]);
                    $ht->increment('posts_count');
                    $post->hashtags()->syncWithoutDetaching([$ht->id]);
                }

                // ── Real Reactions ────────────────────────────────────────────────
                $likeUsers = $allUsers->random(min($pd['likes'], $allUsers->count()));
                $sparkUsers = $allUsers->except($likeUsers->pluck('id')->toArray())->random(min($pd['sparks'], $allUsers->count() - $likeUsers->count()));
                foreach ($likeUsers as $u) {
                    Reaction::firstOrCreate(['user_id'=>$u->id,'post_id'=>$post->id,'type'=>'like']);
                }
                foreach ($sparkUsers as $u) {
                    Reaction::firstOrCreate(['user_id'=>$u->id,'post_id'=>$post->id,'type'=>'spark']);
                }
                $post->updateQuietly(['likes_count' => $likeUsers->count(), 'sparks_count' => $sparkUsers->count()]);

                // ── Real Comments with replies ────────────────────────────────────
                $commentUsers = $allUsers->random(min($pd['comments'], $allUsers->count()));
                $commentModels = collect();
                foreach ($commentUsers as $cu) {
                    $cm = Comment::create([
                        'post_id'  => $post->id,
                        'user_id'  => $cu->id,
                        'body'     => fake()->randomElement([
                            "Super initiative ! 👏",
                            "Merci pour le partage.",
                            "Est-ce qu'on peut s'inscrire en ligne ?",
                            "Très utile, merci.",
                            "Je serai présent.",
                            "Bonne idée, je vote pour.",
                            "Quand est-ce que les résultats tombent ?",
                            "Je l'ai fait hier, c'était pas facile.",
                            "Quelqu'un peut m'expliquer l'exercice 3 ?",
                            "J'ai adoré le livre, je le recommande.",
                            "Est-ce qu'il y a une date limite ?",
                            "Je suis pas d'accord avec le point 2.",
                            "Bravo à toute l'équipe !",
                            "On peut avoir une extension ?",
                        ]),
                        'created_at' => now()->subDays(rand(0, 14))->subHours(rand(0, 23)),
                    ]);
                    $commentModels->push($cm);
                }
                // Add a few replies
                if ($commentModels->count() >= 2) {
                    $parentCount = min(2, intdiv($commentModels->count(), 2));
                    $parents = $commentModels->random($parentCount);
                    foreach ($parents as $parent) {
                        $replyAuthor = $allUsers->random();
                        Comment::create([
                            'post_id'   => $post->id,
                            'user_id'   => $replyAuthor->id,
                            'body'      => fake()->randomElement([
                                "Tout à fait d'accord.",
                                "Je confirme !",
                                "Je peux t'aider si tu veux.",
                                "Oui, j'ai vu ça aussi.",
                                "Merci pour la réponse.",
                            ]),
                            'parent_id' => $parent->id,
                            'created_at'=> $parent->created_at->addMinutes(rand(10, 360)),
                        ]);
                    }
                }
                $post->updateQuietly(['comments_count' => Comment::where('post_id', $post->id)->whereNull('parent_id')->count()]);

                // ── Poll ───────────────────────────────────────────────────────────
                if (!empty($pd['poll'])) {
                    $att = PostAttachment::create(['post_id'=>$post->id,'kind'=>'poll','sort_order'=>0]);
                    $poll = Poll::create(['attachment_id'=>$att->id,'question'=>$pd['poll_q'] ?? 'Question ?','ends_at'=>now()->addDays(3)]);
                    foreach ($pd['poll_opts'] ?? ['Option A','Option B','Option C'] as $i=>$opt) {
                        PollOption::create(['poll_id'=>$poll->id,'label'=>$opt,'sort_order'=>$i,'votes_count'=>rand(3, 20)]);
                    }
                }
            }

            // ── Events ─────────────────────────────────────────────────────────────
            $eventData = [
                ['Semaine des sciences','Grande salle','blue',now()->addDays(2)],
                ['Sortie Musée YSL Marrakech','Marrakech','atlas',now()->addDays(5)],
                ['Concours Robotique Ifrane','Ifrane','terracotta',now()->addDays(6)],
                ['Bal de fin d\'année',$school->name,'saffron',now()->addMonths(2)],
            ];
            foreach ($eventData as [$title,$loc,$color,$date]) {
                Event::create(['school_id'=>$school->id,'created_by'=>$dir->id,'title'=>$title,'location'=>$loc,'color'=>$color,'starts_at'=>$date]);
            }

            // ── Messages ───────────────────────────────────────────────────────────
            $conversations = [
                ['from'=>$students->first(), 'to'=>$teachers->first(), 'body'=>'Bonjour M. El Idrissi, je n\'ai pas compris l\'exercice 4 du DM. Pouvez-vous m\'aider ?'],
                ['from'=>$teachers->first(), 'to'=>$students->first(), 'body'=>'Bien sûr Yasmine. Passe me voir demain en perm, je t\'expliquerai.'],
                ['from'=>$students->get(3), 'to'=>$students->first(), 'body'=>'T\'as fini le projet robot ? J\'ai besoin d\'aide pour la partie code.'],
                ['from'=>$students->first(), 'to'=>$students->get(3), 'body'=>'Oui, je peux t\'aider ce soir vers 18h si tu veux.'],
                ['from'=>$dir, 'to'=>$teachers->first(), 'body'=>'Bonjour M. El Idrissi, pouvez-vous préparer le rapport de fin d\'année pour la semaine prochaine ?'],
                ['from'=>$teachers->first(), 'to'=>$dir, 'body'=>'Bien sûr Mme la directrice, je m\'en occupe.'],
            ];
            foreach ($conversations as $conv) {
                Message::create([
                    'sender_id'    => $conv['from']->id,
                    'recipient_id' => $conv['to']->id,
                    'body'         => $conv['body'],
                    'read_at'      => rand(0, 1) ? now()->subHours(rand(1, 48)) : null,
                    'created_at'   => now()->subDays(rand(0, 7))->subHours(rand(0, 12)),
                ]);
            }
        }

        // ── Badges for Yasmine Bennani ────────────────────────────────────────
        $yasmine = User::where('name','Yasmine Bennani')->where('school_id',$mainSchool->id)->first();
        if ($yasmine) {
            foreach ([
                ["1er prix Robo Ifrane '25",'saffron'],
                ['Mention TB · DM série 6','blue'],
                ['100 jours de série','atlas'],
                ['Délégué de classe','terracotta'],
            ] as [$label,$color]) {
                Badge::create(['user_id'=>$yasmine->id,'label'=>$label,'color'=>$color,'awarded_at'=>now()->subMonths(rand(1,6))]);
            }
        }

        $this->command->info('✓ Seeding complete! Test accounts:');
        $this->command->info('  student@majakker.ma  / password');
        $this->command->info('  teacher@majakker.ma  / password');
        $this->command->info('  director@majakker.ma / password');
        $this->command->info('  admin@majakker.ma    / password');
    }
}
