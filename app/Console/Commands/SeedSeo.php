<?php

namespace App\Console\Commands;

use App\Models\SeoSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class SeedSeo extends Command
{
    protected $signature = 'app:seed-seo';
    protected $description = 'Seed SEO settings with random meta descriptions';

    public function handle(): void
    {
        $descriptions = [
            'landing_meta_description' => [
                'Forrer Zimmermann Architekten – Architektur mit Mass und Materialität, verankert im Schweizer Bauhandwerk.',
                'Architekturbüro Forrer Zimmermann: Wohn- und Gewerbebauten, Sanierungen und Neubauten mit präziser Detailkultur.',
                'Wir entwerfen Bauten, die ihrem Ort verpflichtet sind – ruhig, robust und auf das Wesentliche reduziert.',
                'Forrer Zimmermann steht für eine Architektur, die Konstruktion, Material und Atmosphäre gleichwertig denkt.',
            ],
            'projects_meta_description' => [
                'Eine Auswahl realisierter Projekte: Wohnbauten, öffentliche Bauten und Umbauten aus über zwanzig Jahren Bürotätigkeit.',
                'Projekte von Forrer Zimmermann Architekten – vom Einfamilienhaus bis zum öffentlichen Bau, dokumentiert in Bild und Text.',
                'Ausgewählte Bauten unseres Ateliers: präzise im Detail, klar in der Setzung, ruhig in der Wirkung.',
                'Eine kuratierte Auswahl unserer Arbeiten – Neubauten, Sanierungen und Wettbewerbsbeiträge im Überblick.',
            ],
            'werkliste_meta_description' => [
                'Vollständige Werkliste der von Forrer Zimmermann Architekten realisierten und projektierten Bauten.',
                'Chronologisches Verzeichnis aller Projekte des Ateliers Forrer Zimmermann seit der Bürogründung.',
                'Werkliste: realisierte Bauten, laufende Projekte und Wettbewerbe von Forrer Zimmermann Architekten.',
                'Übersicht sämtlicher Bauvorhaben unseres Ateliers – Auftragsart, Ort und Realisierungsjahr auf einen Blick.',
            ],
            'profile_meta_description' => [
                'Profil und Haltung des Architekturbüros Forrer Zimmermann – Werte, Arbeitsweise und gestalterische Position.',
                'Über das Atelier Forrer Zimmermann: Geschichte, Auffassung von Architektur und Verständnis vom Beruf.',
                'Unser Profil: ein partnerschaftlich geführtes Atelier mit Fokus auf das gebaute Detail und den Ort.',
                'Forrer Zimmermann – ein Atelier, das Architektur als langfristige Verantwortung gegenüber Bauherrschaft und Umfeld versteht.',
            ],
            'team_meta_description' => [
                'Das Team von Forrer Zimmermann Architekten – Architektinnen, Architekten und Mitarbeitende des Ateliers.',
                'Lernen Sie die Mitarbeitenden unseres Architekturbüros kennen – Profile, Rollen und Kontakte.',
                'Wer wir sind: das Team hinter den Projekten von Forrer Zimmermann Architekten.',
                'Die Köpfe unseres Ateliers – Partner und Mitarbeitende, die unsere Projekte gemeinsam tragen.',
            ],
            'jobs_meta_description' => [
                'Offene Stellen und Praktika im Architekturbüro Forrer Zimmermann – aktuelle Ausschreibungen im Überblick.',
                'Karriere bei Forrer Zimmermann: Jobs für Architektinnen, Architekten und Praktikanten in unserem Atelier.',
                'Wir suchen engagierte Mitarbeitende – aktuelle Stellenangebote und Praktikumsmöglichkeiten.',
                'Stellenangebote unseres Ateliers: arbeiten Sie mit uns an präzise gedachten Architekturprojekten.',
            ],
            'contact_meta_description' => [
                'Kontakt zu Forrer Zimmermann Architekten – Adresse, Telefonnummer und Anfahrt zu unserem Atelier.',
                'Nehmen Sie Kontakt auf: Anschrift, E-Mail und Telefon des Architekturbüros Forrer Zimmermann.',
                'So erreichen Sie uns: Kontaktangaben, Bürozeiten und Anfahrtsplan zum Atelier Forrer Zimmermann.',
                'Kontakt und Standort des Ateliers Forrer Zimmermann Architekten – wir freuen uns auf Ihre Anfrage.',
            ],
        ];

        $attributes = [];
        foreach ($descriptions as $field => $pool) {
            $attributes[$field] = Arr::random($pool);
        }

        SeoSetting::firstOrCreate(['id' => 1], $attributes);

        $this->info('SEO settings seeded with random meta descriptions.');
    }
}
