<?php
namespace Database\Seeders;
use App\Models\{User,School,Group,Post,PostAttachment,Poll,PollOption,Event,Badge,Hashtag};
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

            // Groups
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

            // Add users to groups
            $groupModels->each(function($grp) use($students,$teachers) {
                $members = $students->random(min(10,$students->count()))->push($teachers->first());
                $members->each(fn($u)=>$grp->members()->syncWithoutDetaching([$u->id=>['role'=>'member']]));
                $grp->update(['member_count'=>$members->count()]);
            });

            // Posts
            $seedPosts = [
                ['author'=>$dir,'title'=>'Semaine des sciences — du 18 au 22 mai','body'=>"Cher·e·s élèves, professeur·e·s,\n\nLa Semaine des sciences débute lundi avec une conférence de Pr. Hassan Aourag (UM6P) en grande salle. Inscriptions via le lien ci-dessous.",'is_pinned'=>true,'is_announcement'=>true,'tags'=>['sciences','majakker'],'likes'=>184,'comments'=>23,'sparks'=>41],
                ['author'=>$teachers->first(),'title'=>'DM 7 — Suites et raisonnement par récurrence','body'=>"Voici le DM à rendre pour vendredi 16 mai. Les exercices 3 et 5 sont prioritaires.",'is_announcement'=>true,'tags'=>['maths','2bac'],'likes'=>42,'comments'=>18,'sparks'=>7],
                ['author'=>$students->first(),'body'=>"On a fini de souder le bras articulé pour le concours d'Ifrane ! Reste à câbler les servos demain.",'tags'=>['robotique','concours'],'likes'=>28,'comments'=>9,'sparks'=>12],
                ['author'=>$teachers->get(1),'body'=>"Petit sondage avant le contrôle de jeudi : sur quel chapitre voulez-vous une révision en classe ?",'tags'=>['svt'],'likes'=>11,'comments'=>4,'sparks'=>2,'poll'=>true],
                ['author'=>$students->get(1),'body'=>"Quelqu'un a la fiche de M. Berrada sur Bergson ? Je l'ai cherchée partout sur le drive.",'tags'=>['philo','tbac'],'likes'=>6,'comments'=>14,'sparks'=>1],
                ['author'=>$students->get(2),'title'=>'Bal de fin d\'année — vote du thème','body'=>"Le conseil des élèves ouvre le vote pour le thème du bal du 28 juin.",'tags'=>['bal2026','majakker'],'likes'=>96,'comments'=>31,'sparks'=>18,'poll'=>true],
                ['author'=>$dir,'body'=>"Nouveau dans nos rayons : « Les Fondations » d'Asimov en édition intégrale.",'tags'=>['bibliothèque'],'likes'=>54,'comments'=>6,'sparks'=>22],
            ];
            foreach ($seedPosts as $pd) {
                $post = Post::create([
                    'user_id'        => $pd['author']->id,
                    'school_id'      => $school->id,
                    'group_id'       => null,
                    'title'          => $pd['title'] ?? null,
                    'body'           => $pd['body'],
                    'is_pinned'      => $pd['is_pinned'] ?? false,
                    'is_announcement'=> $pd['is_announcement'] ?? false,
                    'visibility'     => 'school',
                    'likes_count'    => $pd['likes'] ?? 0,
                    'comments_count' => $pd['comments'] ?? 0,
                    'sparks_count'   => $pd['sparks'] ?? 0,
                ]);
                foreach ($pd['tags'] ?? [] as $tag) {
                    $ht = Hashtag::firstOrCreate(['name'=>$tag]);
                    $ht->increment('posts_count');
                    $post->hashtags()->syncWithoutDetaching([$ht->id]);
                }
                if (!empty($pd['poll'])) {
                    $att = PostAttachment::create(['post_id'=>$post->id,'kind'=>'poll','sort_order'=>0]);
                    $poll = Poll::create(['attachment_id'=>$att->id,'question'=>'Sur quel chapitre faut-il revenir ?','ends_at'=>now()->addDays(3)]);
                    foreach (['Option A','Option B','Option C'] as $i=>$opt) {
                        PollOption::create(['poll_id'=>$poll->id,'label'=>$opt,'sort_order'=>$i,'votes_count'=>rand(5,30)]);
                    }
                }
            }

            // Events
            $eventData = [
                ['Semaine des sciences','Grande salle','blue',now()->addDays(2)],
                ['Sortie Musée YSL Marrakech','Marrakech','atlas',now()->addDays(5)],
                ['Concours Robotique Ifrane','Ifrane','terracotta',now()->addDays(6)],
                ['Bal de fin d\'année',''.$school->name,'saffron',now()->addMonths(2)],
            ];
            foreach ($eventData as [$title,$loc,$color,$date]) {
                Event::create(['school_id'=>$school->id,'created_by'=>$dir->id,'title'=>$title,'location'=>$loc,'color'=>$color,'starts_at'=>$date]);
            }
        }

        // Badges for Yasmine Bennani
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
