<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\Strand;
use App\Models\StrandSubject;
use Illuminate\Database\Seeder;

class AllShsSubjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting SHS Subjects Seeding...');

        // Seed STEM subjects
        $this->seedStemSubjects();
        
        // Seed ABM subjects
        $this->seedAbmSubjects();
        
        // Seed HUMSS subjects
        $this->seedHumssSubjects();
        
        // Seed GAS subjects
        $this->seedGasSubjects();
        
        // Seed TVL-HE subjects
        $this->seedTvlHeSubjects();

        $this->command->info('✅ All SHS subjects seeded successfully!');
    }

    /**
     * Seed STEM subjects
     */
    private function seedStemSubjects(): void
    {
        $strand = Strand::where('code', 'STEM')->first();
        
        if (!$strand) {
            $this->command->warn('STEM strand not found. Skipping...');
            return;
        }

        $this->command->info('Seeding STEM subjects...');

        // Core Curriculum subjects
        $coreSubjects = [
            ['code' => 'STEM-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-SCI103', 'name' => 'Earth Science', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'STEM-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track subjects
        $appliedSubjects = [
            ['code' => 'STEM-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-FIL201', 'name' => 'Filipino sa Piling Larang (Akademik)', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized subjects (STEM)
        $specializedSubjects = [
            ['code' => 'STEM-PCHEM101', 'name' => 'Pre-Calculus', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-CHEM101', 'name' => 'General Chemistry 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-CHEM102', 'name' => 'General Chemistry 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-BIO101', 'name' => 'General Biology 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-BIO102', 'name' => 'General Biology 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-PHY101', 'name' => 'General Physics 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'STEM-PHY102', 'name' => 'General Physics 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'STEM-CAL101', 'name' => 'Basic Calculus', 'units' => 3, 'semester' => '2nd'],
        ];

        $this->createSubjectsAndLink($coreSubjects, 'core', $strand);
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $strand);
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $strand);

        $this->command->info('✓ STEM subjects completed');
    }

    /**
     * Seed ABM subjects
     */
    private function seedAbmSubjects(): void
    {
        $strand = Strand::where('code', 'ABM')->first();
        
        if (!$strand) {
            $this->command->warn('ABM strand not found. Skipping...');
            return;
        }

        $this->command->info('Seeding ABM subjects...');

        // Core Curriculum subjects (same as other strands)
        $coreSubjects = [
            ['code' => 'ABM-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'ABM-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track subjects
        $appliedSubjects = [
            ['code' => 'ABM-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-RES101', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-FIL201', 'name' => 'Filipino sa Piling Larang (Akademik)', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized subjects (ABM)
        $specializedSubjects = [
            ['code' => 'ABM-ACC101', 'name' => 'Fundamentals of Accountancy, Business and Management 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-ACC102', 'name' => 'Fundamentals of Accountancy, Business and Management 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-BUS101', 'name' => 'Business Ethics and Social Responsibility', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-BUS102', 'name' => 'Business Finance', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-BUS103', 'name' => 'Business Marketing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-BUS104', 'name' => 'Business Mathematics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-BUS105', 'name' => 'Organization and Management', 'units' => 3, 'semester' => '1st'],
            ['code' => 'ABM-BUS106', 'name' => 'Principles of Marketing', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'ABM-ACC201', 'name' => 'Applied Economics', 'units' => 3, 'semester' => '1st'],
        ];

        $this->createSubjectsAndLink($coreSubjects, 'core', $strand);
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $strand);
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $strand);

        $this->command->info('✓ ABM subjects completed');
    }

    /**
     * Seed HUMSS subjects
     */
    private function seedHumssSubjects(): void
    {
        $strand = Strand::where('code', 'HUMSS')->first();
        
        if (!$strand) {
            $this->command->warn('HUMSS strand not found. Skipping...');
            return;
        }

        $this->command->info('Seeding HUMSS subjects...');

        // Core Curriculum subjects
        $coreSubjects = [
            ['code' => 'HUMSS-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'HUMSS-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track subjects
        $appliedSubjects = [
            ['code' => 'HUMSS-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-FIL201', 'name' => 'Filipino sa Piling Larang (Akademik)', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized subjects (HUMSS)
        $specializedSubjects = [
            ['code' => 'HUMSS-CW101', 'name' => 'Creative Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-CW102', 'name' => 'Creative Nonfiction', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-SOC201', 'name' => 'Disciplines and Ideas in the Social Sciences', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-SOC202', 'name' => 'Disciplines and Ideas in the Applied Social Sciences', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-PHILO201', 'name' => 'Trends, Networks and Critical Thinking in the 21st Century', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-COMM101', 'name' => 'Community Engagement, Solidarity and Citizenship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'HUMSS-WORLD101', 'name' => 'World Religions and Belief Systems', 'units' => 3, 'semester' => '1st'],
            ['code' => 'HUMSS-HIS101', 'name' => 'Philippine Politics and Governance', 'units' => 3, 'semester' => '2nd'],
        ];

        $this->createSubjectsAndLink($coreSubjects, 'core', $strand);
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $strand);
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $strand);

        $this->command->info('✓ HUMSS subjects completed');
    }

    /**
     * Seed GAS subjects
     */
    private function seedGasSubjects(): void
    {
        $strand = Strand::where('code', 'GAS')->first();
        
        if (!$strand) {
            $this->command->warn('GAS strand not found. Skipping...');
            return;
        }

        $this->command->info('Seeding GAS subjects...');

        // Core Curriculum subjects
        $coreSubjects = [
            ['code' => 'GAS-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'GAS-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track subjects
        $appliedSubjects = [
            ['code' => 'GAS-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-FIL201', 'name' => 'Filipino sa Piling Larang', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized subjects (GAS) - Electives from various tracks
        $specializedSubjects = [
            ['code' => 'GAS-HUM101', 'name' => 'Humanities 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-HUM102', 'name' => 'Humanities 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-SOC201', 'name' => 'Social Science 1', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-SOC202', 'name' => 'Social Science 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'GAS-APPSCI101', 'name' => 'Applied Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'GAS-BUS101', 'name' => 'Business Math', 'units' => 3, 'semester' => '2nd'],
        ];

        $this->createSubjectsAndLink($coreSubjects, 'core', $strand);
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $strand);
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $strand);

        $this->command->info('✓ GAS subjects completed');
    }

    /**
     * Seed TVL-HE subjects
     */
    private function seedTvlHeSubjects(): void
    {
        $strand = Strand::where('code', 'TVL-HE')->first();
        
        if (!$strand) {
            $this->command->warn('TVL-HE strand not found. Skipping...');
            return;
        }

        $this->command->info('Seeding TVL-HE subjects...');

        // Core Curriculum subjects
        $coreSubjects = [
            ['code' => 'TVLHE-ENG101', 'name' => 'Oral Communication', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-ENG102', 'name' => 'Reading and Writing', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-FIL101', 'name' => 'Komunikasyon at Pananaliksik sa Wika at Kulturang Pilipino', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-FIL102', 'name' => 'Pagbasa at Pagsusuri ng Iba\'t Ibang Teksto Tungo sa Pananaliksik', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-LIT101', 'name' => '21st Century Literature from the Philippines and the World', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-ART101', 'name' => 'Contemporary Philippine Arts from the Regions', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-MIL101', 'name' => 'Media and Information Literacy', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-MATH101', 'name' => 'General Mathematics', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-MATH102', 'name' => 'Statistics and Probability', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-SCI101', 'name' => 'Earth and Life Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-SCI102', 'name' => 'Physical Science', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-PHILO101', 'name' => 'Introduction to Philosophy of the Human Person', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-PE101', 'name' => 'Physical Education and Health', 'units' => 2, 'semester' => '1st'],
            ['code' => 'TVLHE-PD101', 'name' => 'Personal Development', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-SOC101', 'name' => 'Understanding Culture, Society, and Politics', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-DRR101', 'name' => 'Disaster Readiness and Risk Reduction', 'units' => 3, 'semester' => '2nd'],
        ];

        // Applied Track subjects
        $appliedSubjects = [
            ['code' => 'TVLHE-ENG201', 'name' => 'English for Academic and Professional Purposes', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-RES101', 'name' => 'Practical Research 1', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-RES102', 'name' => 'Practical Research 2', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-FIL201', 'name' => 'Filipino sa Piling Larang (Tech Voc)', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-ICT101', 'name' => 'Empowerment Technologies', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-ENT101', 'name' => 'Entrepreneurship', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-III101', 'name' => 'Inquiries, Investigations, and Immersion', 'units' => 3, 'semester' => '2nd'],
        ];

        // Specialized subjects (TVL-HE)
        $specializedSubjects = [
            ['code' => 'TVLHE-COOK101', 'name' => 'Cookery NC II', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-COOK102', 'name' => 'Bread and Pastry Production NC II', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-FSRV101', 'name' => 'Food and Beverage Services NC II', 'units' => 3, 'semester' => '1st'],
            ['code' => 'TVLHE-CARE101', 'name' => 'Caregiving NC II', 'units' => 3, 'semester' => '2nd'],
            ['code' => 'TVLHE-WORK101', 'name' => 'Work Immersion / Research / Career Advocacy', 'units' => 3, 'semester' => '2nd'],
        ];

        $this->createSubjectsAndLink($coreSubjects, 'core', $strand);
        $this->createSubjectsAndLink($appliedSubjects, 'applied', $strand);
        $this->createSubjectsAndLink($specializedSubjects, 'specialized', $strand);

        $this->command->info('✓ TVL-HE subjects completed');
    }

    /**
     * Create subjects and link to strand
     */
    private function createSubjectsAndLink(array $subjects, string $type, $strand): void
    {
        foreach ($subjects as $subjectData) {
            $subject = Subject::updateOrCreate(
                ['code' => $subjectData['code']],
                [
                    'name' => $subjectData['name'],
                    'units' => $subjectData['units'],
                    'type' => $type,
                    'semester' => $subjectData['semester'],
                ]
            );

            // Link subject to strand
            StrandSubject::updateOrCreate(
                [
                    'strand_id' => $strand->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'semestral_period' => $subjectData['semester'],
                    'written_works_percentage' => 20,
                    'performance_tasks_percentage' => 60,
                    'quarterly_assessment_percentage' => 20,
                    'is_active' => true,
                ]
            );
        }
    }
}
