<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Models\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SeedProjects extends Command
{
    protected $signature = 'app:seed-projects';
    protected $description = 'Seed projects with dummy images';

    protected array $projects = [
        [
            'title' => 'Nietengasse',
            'location' => 'Zürich',
            'subtitle' => 'Instandsetzung',
            'year' => 2024,
            'description' => <<<TEXT
Über den Gewerberäumen des Haupthauses befinden sich zwei Kleinwohnungen und eine grosszügige, dreigeschossige Maisonette. Grundsätzlich einem schonenden Umgang mit dem Bestand verpflichtet, führten die haustechnisch und energetisch notwendigen Massnahmen dennoch zu einer hohen Eingriffstiefe. Diese Tatsache wurde dazu genutzt, mittels gezielter kleiner Interventionen die Wohnqualität zu erhöhen und klare Raumstrukturen zu schaffen.

Die Wohnküche der Maisonette erhielt ein französisches Fenster, das trotz fehlendem Balkon einen stärkeren Bezug zum Innenhof schafft. Unter dem neu geformten Dach ist ein zusätzliches, gut belichtetes Zimmer entstanden. In der Hoffassade verweisen grossformatige Öffnungen und eine rote Blechverkleidung auf die zeitgenössische Intervention im vierten Obergeschoss. Weitere Gestaltungselemente erzählen von den Veränderungen: Ein Fensterrelief macht die ausserordentliche Wandstärke spürbar; gestreifte Markisen setzen einen spielerischen Akzent im urbanen Hofraum.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 2.31 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Planerwahlverfahren auf Einladung (1. Rang)
Termine: 2022–2024 (Planung und Realisierung)
Auftraggeber: Genossenschaft Dreieck
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-2.jpg', 'fza-dummy-3.jpg', 'fza-dummy-4.jpg'],
        ],
        [
            'title' => 'Musterstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Strangsanierung',
            'year' => 2024,
            'description' => <<<TEXT
Die Strangsanierung der gründerzeitlichen Liegenschaft umfasst den vollständigen Ersatz der haustechnischen Steigzonen sowie die Erneuerung von Bädern und Küchen in 14 Wohnungen. Die Eingriffe wurden so geplant, dass die Mieterschaft etappenweise und mit minimalen Unterbrüchen in den Wohnungen verbleiben konnte.

In jedem Geschoss wurde der Grundriss geringfügig angepasst, um zeitgemässe Sanitärräume zu ermöglichen, ohne den Charakter der Stuckdecken und Parkettböden zu beeinträchtigen. Eine sorgfältig abgestimmte Materialpalette aus geöltem Eichenholz, gefärbtem Zementputz und mattem Messing nimmt Bezug auf die historische Substanz und übersetzt diese in eine zurückhaltende Gegenwart.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 3.85 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2022–2024 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-5.jpg', 'fza-dummy-6.jpg', 'fza-dummy-7.jpg', 'fza-dummy-8.jpg', 'fza-dummy-9.jpg'],
        ],
        [
            'title' => 'Landwirtschaftsbetrieb Hagenbuchrain',
            'location' => 'Zürich',
            'subtitle' => '1. Rang, Planerwahlverfahren',
            'year' => 2023,
            'description' => <<<TEXT
Der bestehende Landwirtschaftsbetrieb am Stadtrand von Zürich soll baulich und betrieblich auf die Anforderungen einer zeitgemässen, stadtnahen Landwirtschaft ausgerichtet werden. Das Projekt schlägt vor, die historischen Volumen mit ihrer charakteristischen Hofbildung zu erhalten und durch zwei präzise gesetzte Neubauten zu ergänzen.

Ein neuer, einfacher Stallbau in Holzkonstruktion nimmt die Tierhaltung auf und schafft mit einem grosszügigen Vordach einen wettergeschützten Aussenraum. Der zweite Neubau beherbergt Hofladen und Verarbeitung und vermittelt zwischen Hofraum und öffentlicher Erschliessung. Die Materialisierung in vorpatiniertem Holz und Beton ordnet sich der bestehenden Hofstruktur unter und schreibt sie selbstverständlich fort.
TEXT,
            'info' => <<<TEXT
Status: Studie
Gesamtbaukosten: CHF 6.40 Mio. (BKP 1–9)
Leistungen: Vorprojekt
Auftragsart: Planerwahlverfahren auf Einladung (1. Rang)
Termine: 2023 (Wettbewerb), Realisierung offen
Auftraggeber: Stadt Zürich, Liegenschaftenverwaltung
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-10.jpg', 'fza-dummy-11.jpg', 'fza-dummy-12.jpg', 'fza-dummy-1.jpg', 'fza-dummy-2.jpg', 'fza-dummy-3.jpg'],
        ],
        [
            'title' => 'Schulraumerweiterung Ebnet',
            'location' => 'Abtwil',
            'subtitle' => 'Projektwettbewerb auf Präqualifikation',
            'year' => 2023,
            'description' => <<<TEXT
Die Schulanlage Ebnet wird durch einen kompakten Erweiterungsbau ergänzt, der die bestehende Pausenhoffigur stärkt und gleichzeitig ein eigenständiges Gegenüber zur historischen Schulanlage bildet. Drei zusätzliche Klassenzimmer mit Gruppenräumen sowie eine Mehrzweckhalle werden in einem viergeschossigen Volumen organisiert.

Die Tragstruktur in Holzelementbauweise erlaubt eine kurze Bauzeit und ermöglicht die Beibehaltung des laufenden Schulbetriebs. Eine gefaltete Vorhangfassade aus eloxierten Aluminiumprofilen verleiht dem neuen Schulhaus eine markante, aber zurückhaltende Erscheinung. Im Inneren prägen sichtbare Holzoberflächen und farbige Linoleumböden eine ruhige, lernfreundliche Atmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 9.20 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Projektwettbewerb auf Präqualifikation
Termine: 2023 (Wettbewerb)
Auftraggeber: Politische Gemeinde Gaiserwald
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-4.jpg', 'fza-dummy-5.jpg', 'fza-dummy-6.jpg', 'fza-dummy-7.jpg'],
        ],
        [
            'title' => 'Kindergarten Breiti',
            'location' => 'Dietikon',
            'subtitle' => 'Instandstellung und Anbau',
            'year' => 2022,
            'description' => <<<TEXT
Der bestehende Doppelkindergarten aus den 1970er-Jahren wurde energetisch saniert und um eine zusätzliche Abteilung erweitert. Der Anbau setzt sich als zweigeschossiges Holzvolumen sorgfältig vom Bestand ab und nimmt gleichzeitig dessen flachgeneigte Dachform auf.

Im Inneren wurde die ursprüngliche Materialisierung mit Sichtbacksteinwänden und Holztäfern weitgehend erhalten und durch zeitgemässe Eingriffe ergänzt. Ein neuer, geschützter Eingangsbereich verbindet alle drei Abteilungen mit dem Aussenraum und bildet zugleich eine wettertaugliche Spielnische. Der grosszügige, nun zusammenhängende Gartenraum wurde landschaftsarchitektonisch neu gefasst.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 4.10 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2020–2022 (Planung und Realisierung)
Auftraggeber: Stadt Dietikon, Hochbau
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-8.jpg', 'fza-dummy-9.jpg', 'fza-dummy-10.jpg', 'fza-dummy-11.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Mehrzweckraum & Turnhalle',
            'location' => 'Flühli',
            'subtitle' => 'Studienauftrag 2. Rang',
            'year' => 2022,
            'description' => <<<TEXT
Das Projekt schlägt eine kompakte Anordnung von Mehrzweckraum und Einfachturnhalle in einem gemeinsamen, langgestreckten Volumen vor. Durch die Setzung am westlichen Rand des Schulareals entsteht ein klar gefasster, zusammenhängender Pausenplatz. Die topografische Eingliederung folgt der natürlichen Hangkante und reduziert die wahrgenommene Bauhöhe.

Die Tragstruktur in Brettschichtholz und der hinterlüftete Holzschirm aus heimischer Weisstanne nehmen Bezug auf die regionale Bautradition. Im Inneren öffnen sich Foyer und Mehrzweckraum über eine grosse Faltwand zueinander, sodass je nach Anlass ein einziger, festlicher Saal entstehen kann.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 7.85 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienauftrag auf Einladung (2. Rang)
Termine: 2022 (Wettbewerb)
Auftraggeber: Gemeinde Flühli
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-2.jpg', 'fza-dummy-4.jpg', 'fza-dummy-6.jpg', 'fza-dummy-8.jpg'],
        ],
        [
            'title' => 'Kindergarten Ennetbach Netstal',
            'location' => 'Netstal',
            'subtitle' => '3. Rang, offenes Planerwahlverfahren',
            'year' => 2021,
            'description' => <<<TEXT
Der neue Doppelkindergarten ergänzt die bestehende Schulanlage und nimmt als pavillonartiges Volumen Bezug auf die umliegende Wohnbebauung. Ein gemeinsam genutzter Eingangsbereich erschliesst beide Abteilungen und schafft einen geschützten, wettertauglichen Treffpunkt für Kinder und Eltern.

Die Holzkonstruktion mit ihrer feingliedrigen Lattenfassade lässt einen freundlichen, kleinmassstäblichen Charakter entstehen. Innen prägen helle, im Sommer gut beschattete Gruppenräume das Bild; eine niedrige, durchlaufende Fensterbank lädt zum Verweilen ein und vermittelt zwischen Innen und Aussen.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 3.60 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offenes Planerwahlverfahren (3. Rang)
Termine: 2021 (Wettbewerb)
Auftraggeber: Gemeinde Glarus
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-3.jpg', 'fza-dummy-5.jpg', 'fza-dummy-7.jpg', 'fza-dummy-9.jpg'],
        ],
        [
            'title' => 'Stammhäuser GeHo',
            'location' => 'Zürich',
            'subtitle' => 'Studienwettbewerb auf Einladung',
            'year' => 2021,
            'description' => <<<TEXT
Die historisch gewachsene Häuserzeile am Stammhausareal soll behutsam saniert und um neue Wohnungen ergänzt werden. Das Projekt entwickelt eine massgeschneiderte Strategie pro Haus, die zwischen baulicher Substanz, energetischer Notwendigkeit und Wohnqualität sorgfältig vermittelt.

Aufstockungen in Holz mit zinkverkleideten Dachformen führen die Gebäudereihe stadträumlich weiter, ohne die kleinteilige Körnung zu zerstören. Die innere Reorganisation erschliesst attraktive Maisonettewohnungen und macht die historische Holzbalkenstruktur wieder erlebbar.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 11.80 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienwettbewerb auf Einladung
Termine: 2021 (Wettbewerb)
Auftraggeber: Baugenossenschaft GeHo
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-10.jpg', 'fza-dummy-12.jpg', 'fza-dummy-2.jpg', 'fza-dummy-4.jpg', 'fza-dummy-6.jpg', 'fza-dummy-8.jpg'],
        ],
        [
            'title' => 'Schulhaus Borrweg',
            'location' => 'Zürich',
            'subtitle' => 'Offener Projektwettbewerb',
            'year' => 2019,
            'description' => <<<TEXT
Der Neubau besetzt eine markante Hangkante und ergänzt die bestehende Schulanlage zu einem klar gefassten Ensemble. Vier Geschosse mit kompakter Grundrisstypologie organisieren ein Raumprogramm aus Klassen-, Gruppen- und Spezialräumen entlang einer mehrfach belichteten Lernlandschaft.

Eine vorgesetzte Pfeilerstruktur aus Sichtbeton bildet umlaufende Aussenräume und nimmt die topografische Setzung der Anlage auf. Im Inneren schaffen geölte Eichenböden, lichte Putzwände und sorgfältig disponierte Sichtbezüge eine konzentrierte und gleichzeitig grosszügige Lernatmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 22.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb
Termine: 2019 (Wettbewerb)
Auftraggeber: Stadt Zürich, Amt für Hochbauten
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-11.jpg', 'fza-dummy-1.jpg', 'fza-dummy-3.jpg', 'fza-dummy-5.jpg'],
        ],
        [
            'title' => 'Berufsschule Ziegelbrücke',
            'location' => 'Ziegelbrücke',
            'subtitle' => 'Offener Projektwettbewerb',
            'year' => 2019,
            'description' => <<<TEXT
Die neue Berufsschule positioniert sich als kompaktes, fünfgeschossiges Volumen am Knotenpunkt zwischen Bahnhof und Industrieareal. Das Gebäude reagiert mit zwei unterschiedlichen Fassadenausbildungen auf die heterogenen städtebaulichen Situationen seiner Umgebung.

Eine zentrale Halle erschliesst alle Geschosse und dient gleichzeitig als Aufenthalts- und Lernbereich. Die robuste Materialisierung aus Sichtbeton, Stahlgeländern und geöltem Industrieparkett verweist auf den Werkstättencharakter der Schule und schafft langlebige, identitätsstiftende Räume.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 28.50 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb
Termine: 2019 (Wettbewerb)
Auftraggeber: Kanton Glarus
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-7.jpg', 'fza-dummy-9.jpg', 'fza-dummy-11.jpg', 'fza-dummy-2.jpg', 'fza-dummy-4.jpg'],
        ],
        [
            'title' => 'Forensikstation',
            'location' => 'Wil',
            'subtitle' => 'Offener Projektwettbewerb',
            'year' => 2019,
            'description' => <<<TEXT
Die forensische Abteilung der Klinik wird in einem ringförmigen, eingeschossigen Pavillon untergebracht, der einen geschützten Therapiegarten umschliesst. Die strenge Geometrie schafft Orientierung und Übersicht und vermeidet zugleich die Anmutung einer geschlossenen Anstalt.

Patientenzimmer, Therapieräume und Pflegestützpunkte sind so disponiert, dass differenzierte Aufenthaltsqualitäten entstehen. Die Materialisierung aus Holz, Lehm und gewachstem Klinker bewusst eine wohnliche, sinnlich erfahrbare Atmosphäre. Sicherheitstechnische Anforderungen werden integral und unaufdringlich gelöst.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 18.90 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb
Termine: 2019 (Wettbewerb)
Auftraggeber: Kanton St. Gallen, Hochbauamt
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-6.jpg', 'fza-dummy-8.jpg', 'fza-dummy-10.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Marktplatz und Bohl',
            'location' => 'St. Gallen',
            'subtitle' => 'Offener Projektwettbewerb',
            'year' => 2019,
            'description' => <<<TEXT
Die Neugestaltung der zusammenhängenden Platzfolge im Zentrum von St. Gallen versteht den öffentlichen Raum als kontinuierliches, durchlässiges Gewebe. Ein einheitlicher, präzise verlegter Natursteinbelag verbindet Marktplatz und Bohl, ohne ihre je eigene räumliche Identität aufzugeben.

Wenige, präzis gesetzte Eingriffe – eine neue Brunnenfigur, drei Baumgruppen und eine durchlaufende Bankfigur – schaffen Aufenthaltsqualität und differenzieren die Nutzungen. Die Materialisierung mit lokalen Sandsteinen knüpft an die historische Stadtbauweise an und erlaubt zugleich eine zeitgenössische, robust nutzbare Platzgestaltung.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 14.20 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb
Termine: 2019 (Wettbewerb)
Auftraggeber: Stadt St. Gallen
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-5.jpg', 'fza-dummy-7.jpg', 'fza-dummy-9.jpg', 'fza-dummy-11.jpg', 'fza-dummy-1.jpg'],
        ],
        [
            'title' => 'Wohnhaus Hofackerstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Aufstockung und Sanierung',
            'year' => 2024,
            'description' => <<<TEXT
Das viergeschossige Wohnhaus aus den späten 1920er-Jahren wurde umfassend saniert und um ein zurückversetztes Attikageschoss in Holzbauweise erweitert. Die Aufstockung interpretiert das ortstypische Mansarddach neu und schafft zwei grosszügige Dachwohnungen mit umlaufenden Terrassen.

Im Bestand wurden die Wohnungsgrundrisse behutsam optimiert, ohne die charakteristischen Raumstrukturen aufzugeben. Originale Eichenparkette, Stuckdecken und Türen wurden restauriert; neue Bäder und Küchen ergänzen den historischen Bestand mit ruhigen, präzise gefügten Einbauten aus Eichenholz und farbig abgestimmtem Linoleum.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 5.60 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2022–2024 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-2.jpg', 'fza-dummy-5.jpg', 'fza-dummy-8.jpg', 'fza-dummy-11.jpg'],
        ],
        [
            'title' => 'Werkhof Greifensee',
            'location' => 'Greifensee',
            'subtitle' => 'Neubau',
            'year' => 2024,
            'description' => <<<TEXT
Der neue Werkhof der Gemeinde fasst Fahrzeughalle, Werkstatt, Material- und Personalbereiche in einem klar gegliederten, eingeschossigen Hallenbau zusammen. Die Setzung am Rand des Industriegebiets nimmt Bezug auf die landwirtschaftlich geprägte Umgebung und schafft einen grosszügigen, gut belichteten Vorplatz.

Die Tragstruktur aus Brettschichtholzbindern überspannt stützenfrei alle Funktionsbereiche und erlaubt eine flexible Belegung. Eine vertikal strukturierte Holzfassade aus heimischer Lärche umfasst das Volumen und reduziert seine Massstäblichkeit zu einer ruhigen, langgestreckten Geste in der Landschaft.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 8.40 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Selektives Verfahren (1. Rang)
Termine: 2021–2024 (Planung und Realisierung)
Auftraggeber: Gemeinde Greifensee
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-6.jpg', 'fza-dummy-9.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Doppelhaus Sunnehof',
            'location' => 'Wädenswil',
            'subtitle' => 'Neubau',
            'year' => 2023,
            'description' => <<<TEXT
Das Doppelhaus für zwei Generationen einer Familie reagiert mit einer leichten Verschiebung der beiden Wohnvolumen auf die Hanglage und die unterschiedliche Belichtung. Jede Wohneinheit verfügt über einen eigenen Eingang und einen privaten Gartenbereich, während ein gemeinsamer Hofraum den verbindenden Mittelpunkt bildet.

Die Tragstruktur aus dämmenden Holzelementen erlaubt eine kurze Bauzeit und einen hohen Vorfertigungsgrad. Eine fein profilierte Schalung aus vorvergrauter Weisstanne fasst beide Häuser zu einer ruhigen Gesamterscheinung zusammen, die sich zurückhaltend in die heterogene Umgebung einfügt.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 1.95 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2021–2023 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-4.jpg', 'fza-dummy-7.jpg', 'fza-dummy-10.jpg'],
        ],
        [
            'title' => 'Gewerbehaus Schöneichstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Umnutzung',
            'year' => 2023,
            'description' => <<<TEXT
Das ehemalige Lagergebäude aus den 1960er-Jahren wurde zu einem flexiblen Gewerbe- und Atelierhaus umgenutzt. Die robuste Betonskelettstruktur erlaubt unterschiedlichste Belegungen; eine durchlaufende Erschliessungsschicht entlang der Längsfassade nimmt sämtliche haustechnischen Anforderungen auf und hält die Mietflächen frei von Installationen.

Der vormals geschlossene Sockel wurde mit grossformatigen Schaufenstern geöffnet und schafft eine direkte Beziehung zum Strassenraum. Eine pulverbeschichtete Stahlkonstruktion mit umlaufenden Galerien erweitert die Gebäudehülle nach aussen und stellt den Bezug zwischen Gewerbe und Quartier her.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 6.80 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2021–2023 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-5.jpg', 'fza-dummy-9.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Pfarrhaus Erlenbach',
            'location' => 'Erlenbach',
            'subtitle' => 'Restaurierung und Umbau',
            'year' => 2023,
            'description' => <<<TEXT
Das denkmalgeschützte Pfarrhaus aus dem 18. Jahrhundert wurde behutsam restauriert und um zeitgemässe Anforderungen ergänzt. In enger Abstimmung mit der Denkmalpflege wurden originale Stuckaturen, Riemenböden und bemalte Holzdecken freigelegt und konservatorisch gesichert.

Im Erdgeschoss nimmt eine neue, leicht abgesenkte Saalsituation Veranstaltungen der Kirchgemeinde auf. Im Obergeschoss wurde die Pfarrwohnung durch das gezielte Entfernen späterer Einbauten wieder in ihrer ursprünglichen Raumfolge erlebbar gemacht. Die haustechnische Ertüchtigung erfolgte konsequent in den Sekundärschichten und lässt die historische Substanz unangetastet.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 3.20 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2020–2023 (Planung und Realisierung)
Auftraggeber: Reformierte Kirchgemeinde Erlenbach
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-2.jpg', 'fza-dummy-6.jpg', 'fza-dummy-11.jpg'],
        ],
        [
            'title' => 'Mehrfamilienhaus Hardstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Ersatzneubau',
            'year' => 2022,
            'description' => <<<TEXT
Der Ersatzneubau ergänzt die heterogene Strassenbebauung um ein präzise gesetztes, fünfgeschossiges Wohnhaus mit zwölf gemeinnützigen Wohnungen. Eine kompakte Mittelganglösung erlaubt grosszügige, durchgesteckte Wohnungstypen mit zwei sich gegenüberliegenden Aussenräumen.

Die tragende Aussenwand aus Mauerwerk wird mit einem mineralischen Putz versehen und nimmt die Bautradition der Umgebung auf. Sorgfältig ausgebildete Loggien fassen die Strassen- und Hoffassaden und schaffen einen vermittelnden Zwischenraum zwischen privatem und öffentlichem Bereich.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 12.40 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Studienauftrag auf Einladung (1. Rang)
Termine: 2019–2022 (Planung und Realisierung)
Auftraggeber: Baugenossenschaft Hardstrasse
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-7.jpg', 'fza-dummy-10.jpg', 'fza-dummy-1.jpg'],
        ],
        [
            'title' => 'Schulanlage Buechwies',
            'location' => 'Bülach',
            'subtitle' => 'Studienauftrag 1. Rang',
            'year' => 2022,
            'description' => <<<TEXT
Die Erweiterung der Schulanlage Buechwies fügt zwei neue Pavillons in die durchgrünte Anlage ein und stärkt damit die ursprüngliche, kleinteilige Bebauungsstruktur. Die Pavillons nehmen je acht Klassenzimmer mit zugehörigen Gruppenräumen auf und sind über eine zentrale, offene Halle erschlossen.

Die Holzkonstruktion aus regional gefertigten Brettstapelelementen erlaubt eine kurze Bauzeit und einen hohen Vorfertigungsgrad. Eine zurückhaltende, vertikal gegliederte Lärchenfassade fügt sich in die parkartige Umgebung ein und betont mit präzise gesetzten Loggien die räumliche Verbindung zwischen Innen und Aussen.
TEXT,
            'info' => <<<TEXT
Status: In Planung
Gesamtbaukosten: CHF 24.60 Mio. (BKP 1–9, geschätzt)
Leistungen: Vorprojekt, Bauprojekt
Auftragsart: Studienauftrag auf Einladung (1. Rang)
Termine: 2022 (Wettbewerb), 2024–2027 (Realisierung)
Auftraggeber: Stadt Bülach
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-4.jpg', 'fza-dummy-8.jpg', 'fza-dummy-12.jpg', 'fza-dummy-2.jpg'],
        ],
        [
            'title' => 'Quartierzentrum Riesbach',
            'location' => 'Zürich',
            'subtitle' => 'Selektiver Wettbewerb',
            'year' => 2022,
            'description' => <<<TEXT
Das neue Quartierzentrum verbindet einen multifunktionalen Saal, Gemeinschaftsräume und ein quartiernahes Café in einem dreigeschossigen Volumen am Quartierplatz. Eine grosszügige, gedeckte Vorzone vermittelt zwischen Strassenraum und Gebäude und kann für Märkte und Veranstaltungen mitgenutzt werden.

Die Tragstruktur aus Holz-Beton-Verbund erlaubt grosse, stützenfreie Räume und ermöglicht eine flexible Belegung. Vorhandene Bäume des angrenzenden Parks werden integriert; im Inneren prägen sichtbare Holzoberflächen, geöltes Eichenparkett und farbige Akzente eine wohnliche, einladende Atmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 9.80 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Selektiver Wettbewerb
Termine: 2022 (Wettbewerb)
Auftraggeber: Stadt Zürich
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-5.jpg', 'fza-dummy-9.jpg', 'fza-dummy-1.jpg'],
        ],
        [
            'title' => 'Doppelkindergarten Aspholz',
            'location' => 'Affoltern',
            'subtitle' => 'Projektwettbewerb 2. Rang',
            'year' => 2021,
            'description' => <<<TEXT
Das Projekt schlägt einen kompakten, eingeschossigen Pavillonbau vor, der über eine zentrale Halle erschlossen ist und die zwei Abteilungen klar voneinander trennt. Die niedrige Gebäudehöhe respektiert den kleinmassstäblichen Charakter der angrenzenden Wohnsiedlung.

Eine gefaltete Holzdachstruktur überspannt die Klassen- und Gruppenräume und schafft eine differenzierte, gut belichtete Innenraumlandschaft. Die hinterlüftete Holzfassade aus heimischer Tanne und die grossen, bodentiefen Fenster lassen das Gebäude als selbstverständlichen Bestandteil der Spielwelt der Kinder erscheinen.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 4.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Projektwettbewerb auf Einladung (2. Rang)
Termine: 2021 (Wettbewerb)
Auftraggeber: Gemeinde Affoltern am Albis
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-6.jpg', 'fza-dummy-10.jpg', 'fza-dummy-3.jpg'],
        ],
        [
            'title' => 'Wohnsiedlung Letzigraben',
            'location' => 'Zürich',
            'subtitle' => 'Studienauftrag',
            'year' => 2021,
            'description' => <<<TEXT
Die neue Wohnsiedlung mit 84 gemeinnützigen Wohnungen formuliert eine klare städtebauliche Geste: Zwei langgestreckte Zeilen fassen einen begrünten, autofreien Hofraum, der als gemeinschaftlicher Aussenraum sämtliche Wohnungen miteinander verbindet.

Eine fein gegliederte Skelettstruktur aus Sichtbeton mit eingestellten Mauerwerksflächen erlaubt eine flexible Anordnung unterschiedlichster Wohnungstypen und schafft eine ruhige, kraftvolle Erscheinung. Loggien und gemeinschaftlich genutzte Dachterrassen erweitern die Wohnflächen und stärken den Bezug zwischen Bewohnerschaft und Aussenraum.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 38.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienauftrag auf Einladung
Termine: 2021 (Wettbewerb)
Auftraggeber: Baugenossenschaft Letzigraben
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-7.jpg', 'fza-dummy-11.jpg', 'fza-dummy-4.jpg', 'fza-dummy-8.jpg'],
        ],
        [
            'title' => 'Genossenschaft Dreieck',
            'location' => 'Zürich',
            'subtitle' => 'Sanierung und Aufstockung',
            'year' => 2020,
            'description' => <<<TEXT
Die Liegenschaft am Rand der Genossenschaftssiedlung wurde umfassend saniert und um ein zurückgesetztes Attikageschoss in Holzbauweise erweitert. Die Aufstockung beherbergt drei Kleinwohnungen, die mit einer kollektiven Dachterrasse einen attraktiven Treffpunkt im Quartier schaffen.

Im Bestand wurden Wohnungsgrundrisse behutsam reorganisiert und die haustechnische Infrastruktur grundlegend erneuert. Charakteristische Bauteile wie Fenster, Türen und Eichenparkette wurden, wo möglich, erhalten und durch sorgfältig abgestimmte Neuteile ergänzt.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 7.60 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2018–2020 (Planung und Realisierung)
Auftraggeber: Genossenschaft Dreieck
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-9.jpg', 'fza-dummy-12.jpg', 'fza-dummy-5.jpg'],
        ],
        [
            'title' => 'Volksschule Heuried',
            'location' => 'Zürich',
            'subtitle' => 'Instandsetzung',
            'year' => 2020,
            'description' => <<<TEXT
Die Volksschule aus den 1950er-Jahren wurde umfassend instand gesetzt, ohne ihren ursprünglichen Charakter zu verändern. Die ortstypische Klinkerfassade wurde fugenweise restauriert; sämtliche Fenster wurden in der ursprünglichen Profilierung neu in Holz-Metall-Konstruktion erstellt.

Im Inneren wurden charakteristische Materialien wie Terrazzoböden, Sichtbeton und Holztäfer freigelegt und konservatorisch behandelt. Eine moderate Anpassung der Klassenraumtypologie sowie der Einbau einer offenen Lernlandschaft im Dachgeschoss schaffen den Anschluss an heutige pädagogische Anforderungen.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 14.20 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Selektives Verfahren (1. Rang)
Termine: 2017–2020 (Planung und Realisierung)
Auftraggeber: Stadt Zürich, Amt für Hochbauten
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-10.jpg', 'fza-dummy-2.jpg', 'fza-dummy-6.jpg', 'fza-dummy-11.jpg'],
        ],
        [
            'title' => 'Atelierhaus Kreis 4',
            'location' => 'Zürich',
            'subtitle' => 'Umbau',
            'year' => 2020,
            'description' => <<<TEXT
Das ehemalige Hinterhausgebäude wurde zu einem fünfgeschossigen Atelierhaus für Künstlerinnen und Kleingewerbe umgebaut. Die offene, stützenarme Struktur des Bestands erlaubt eine flexible, raumhohe Belegung der Geschosse.

Eine neue, aussen liegende Stahlerschliessung erschliesst alle Geschosse direkt vom Hofraum und befreit die Mietflächen von zusätzlichen Erschliessungsschichten. Im Innern bleiben die ursprünglichen Sichtbetonoberflächen, Industriefenster und Bodenplatten erhalten und werden durch wenige präzise Eingriffe in geöltem Eichenholz und schwarz gestrichenem Stahl ergänzt.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 4.60 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2018–2020 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-7.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Schulhaus Im Birch',
            'location' => 'Zürich',
            'subtitle' => 'Erweiterung',
            'year' => 2020,
            'description' => <<<TEXT
Die Erweiterung der Schulanlage erfolgt durch einen kompakten Anbau, der sich über eine verglaste Verbindungshalle an den Bestand anschliesst. Das neue Volumen nimmt vier Klassenzimmer, Gruppenräume und einen Mehrzweckraum auf und stärkt die räumliche Definition des Pausenplatzes.

Die Holzkonstruktion mit hinterlüfteter Lärchenfassade nimmt Bezug auf die parkartige Umgebung. Die innere Raumfolge entlang der Erschliessung wird durch grosszügige Belichtungselemente rhythmisiert und schafft eine helle, lernfreundliche Atmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 6.20 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Studienauftrag auf Einladung (1. Rang)
Termine: 2017–2020 (Planung und Realisierung)
Auftraggeber: Stadt Zürich, Amt für Hochbauten
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-8.jpg', 'fza-dummy-11.jpg', 'fza-dummy-2.jpg'],
        ],
        [
            'title' => 'Bauernhof Tüfwies',
            'location' => 'Bonstetten',
            'subtitle' => 'Umbau und Erweiterung',
            'year' => 2019,
            'description' => <<<TEXT
Der bestehende Bauernhof wurde zu einem zeitgemässen Wohn- und Betriebsensemble umgebaut. Das ehemalige Ökonomiegebäude beherbergt heute zwei grosszügige Wohnungen und ein Atelier; das Wohnhaus wurde sanft instand gesetzt und um einen kompakten Holzanbau erweitert.

Die Eingriffe folgen dem Prinzip der zurückhaltenden Übersetzung: Die ortstypischen Bauformen, die Volumen und die landschaftliche Setzung bleiben erhalten. Neue, präzise gefügte Innenausbauten in geöltem Eichenholz und gewachstem Lehmputz fügen eine ruhige, zeitgenössische Schicht hinzu.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 3.40 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2017–2019 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-4.jpg', 'fza-dummy-9.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Reiheneinfamilienhäuser Friesenberg',
            'location' => 'Zürich',
            'subtitle' => 'Studie',
            'year' => 2019,
            'description' => <<<TEXT
Die Studie untersucht das Potenzial einer behutsamen Verdichtung der Reiheneinfamilienhäuser aus den 1940er-Jahren. Vorgeschlagen wird eine pro Haus differenzierte Strategie aus Sanierung, Aufstockung und Anbau, die den ortstypischen, kleinteiligen Charakter der Siedlung erhält.

Eine modulare Holzelementbauweise erlaubt einen schrittweisen, mietergerechten Eingriff und einen hohen Vorfertigungsgrad. Die Studie zeigt auf, wie durch gezielte kleine Massnahmen erheblicher zusätzlicher Wohnraum geschaffen und gleichzeitig die energetische Performance der Gesamtsiedlung deutlich verbessert werden kann.
TEXT,
            'info' => <<<TEXT
Status: Studie
Gesamtbaukosten: nicht definiert
Leistungen: Machbarkeitsstudie
Auftragsart: Direktauftrag
Termine: 2019
Auftraggeber: Familienheim-Genossenschaft Zürich
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-5.jpg', 'fza-dummy-10.jpg', 'fza-dummy-1.jpg'],
        ],
        [
            'title' => 'Werkstatt Zähringerstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Innenausbau',
            'year' => 2018,
            'description' => <<<TEXT
Die ehemalige Druckerei im Erdgeschoss eines gründerzeitlichen Geschäftshauses wurde zu einer kombinierten Werkstatt und Verkaufsfläche umgenutzt. Eine eingestellte Box aus geöltem Eichenholz organisiert die internen Funktionen und lässt die historische Hülle mit ihren Sichtbacksteinwänden und Stahlträgern unangetastet.

Die Beleuchtung wurde als sichtbares System aus mattschwarzen Schienen ausgebildet und prägt die Atmosphäre des Raumes. Eine durchlaufende Werkbank vermittelt zwischen Werkstatt- und Verkaufsbereich und erlaubt den Kundinnen und Kunden direkten Einblick in die handwerkliche Arbeit.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 0.62 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2017–2018 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-6.jpg', 'fza-dummy-11.jpg'],
        ],
        [
            'title' => 'Dorfschule Egg',
            'location' => 'Egg',
            'subtitle' => 'Offener Projektwettbewerb 4. Rang',
            'year' => 2018,
            'description' => <<<TEXT
Das Projekt setzt an die Stelle des bestehenden, baulich überholten Schulhauses einen kompakten Neubau, der die Setzung im Dorfkern selbstverständlich fortschreibt. Vier Klassenzimmer pro Geschoss sind um eine grosszügige, mehrfach belichtete Lernlandschaft organisiert.

Eine ortstypische Walmdachform fügt das Volumen in das Dorfbild ein und nimmt Bezug auf die umliegenden Gebäude. Die Materialisierung mit verputzten Aussenwänden, geöltem Eichenparkett und Holzdecken schafft eine vertraute und gleichzeitig zeitgenössische Schulatmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 11.50 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb (4. Rang)
Termine: 2018 (Wettbewerb)
Auftraggeber: Gemeinde Egg
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-7.jpg', 'fza-dummy-12.jpg', 'fza-dummy-3.jpg'],
        ],
        [
            'title' => 'Stadthaus Niederdorf',
            'location' => 'Zürich',
            'subtitle' => 'Restaurierung',
            'year' => 2018,
            'description' => <<<TEXT
Das mittelalterliche Stadthaus in der Altstadt wurde umfassend restauriert. In enger Zusammenarbeit mit der Denkmalpflege wurden originale Holzbalkendecken, bemalte Wandflächen und Sandsteinfenstergewände freigelegt, restauriert und in das neue Nutzungsgefüge eingebunden.

Im Erdgeschoss bietet eine kleine Galerie Raum für wechselnde Ausstellungen; in den darüberliegenden Geschossen wurden zwei Stadtwohnungen mit charaktervoller Raumfolge eingerichtet. Die haustechnische Infrastruktur wurde unsichtbar in den Sekundärschichten verlegt und respektiert die wertvolle Bausubstanz vollständig.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 2.85 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2016–2018 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-8.jpg', 'fza-dummy-1.jpg', 'fza-dummy-4.jpg'],
        ],
        [
            'title' => 'Wohnüberbauung Hohlweg',
            'location' => 'Schlieren',
            'subtitle' => 'Studienauftrag auf Einladung',
            'year' => 2018,
            'description' => <<<TEXT
Die neue Wohnüberbauung mit 56 Wohnungen organisiert sich um einen autofreien, parkartigen Hofraum. Drei abgesetzte Volumen reagieren auf die unterschiedlichen Strassen- und Hofsituationen und bilden differenzierte Aussenraumqualitäten.

Eine durchgehende Holzkonstruktion erlaubt grosszügige, durchgesteckte Wohnungstypen und schafft sichtbare Holzoberflächen in allen Wohnräumen. Loggien und gemeinschaftlich genutzte Dachgärten erweitern die individuellen Wohnflächen und fördern das Zusammenleben in der Siedlung.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 26.80 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienauftrag auf Einladung
Termine: 2018 (Wettbewerb)
Auftraggeber: Stadt Schlieren
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-9.jpg', 'fza-dummy-2.jpg', 'fza-dummy-5.jpg'],
        ],
        [
            'title' => 'Gemeindezentrum Oberglatt',
            'location' => 'Oberglatt',
            'subtitle' => 'Projektwettbewerb auf Einladung',
            'year' => 2017,
            'description' => <<<TEXT
Das neue Gemeindezentrum bündelt Verwaltung, Saal und ein quartiernahes Café in einem präzise gesetzten, dreigeschossigen Volumen am Dorfplatz. Eine durchlaufende Erdgeschosshalle vermittelt zwischen den unterschiedlichen Nutzungen und schafft einen wettertauglichen, halböffentlichen Raum.

Die Materialisierung mit lokalen Sandsteinen und sägerohem Eichenholz nimmt Bezug auf die regionale Bautradition. Sorgfältig disponierte Sichtbezüge zwischen Innen- und Aussenraum, Saal und Foyer erlauben unterschiedliche Veranstaltungsformate und stärken die Verankerung des Gebäudes im Quartier.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 13.60 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Projektwettbewerb auf Einladung
Termine: 2017 (Wettbewerb)
Auftraggeber: Gemeinde Oberglatt
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-10.jpg', 'fza-dummy-3.jpg', 'fza-dummy-6.jpg'],
        ],
        [
            'title' => 'Mehrfamilienhaus Idastrasse',
            'location' => 'Zürich',
            'subtitle' => 'Aufstockung',
            'year' => 2017,
            'description' => <<<TEXT
Das gründerzeitliche Mehrfamilienhaus wurde um zwei zusätzliche Geschosse in Holzbauweise aufgestockt. Die Aufstockung interpretiert das ortstypische Mansarddach neu und schafft vier zusätzliche Wohnungen mit bemerkenswerten Aussenräumen.

Im Bestand wurden die Wohnungsgrundrisse behutsam optimiert; charakteristische Bauteile wie Stuckdecken, Eichenparkette und Türen wurden restauriert. Eine neue gemeinschaftlich genutzte Dachterrasse erweitert das Wohnangebot und schafft einen attraktiven Treffpunkt für die Bewohnerschaft.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 4.80 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2015–2017 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-11.jpg', 'fza-dummy-4.jpg', 'fza-dummy-7.jpg'],
        ],
        [
            'title' => 'Schulhaus Brunnenmoos',
            'location' => 'Stäfa',
            'subtitle' => 'Selektiver Wettbewerb',
            'year' => 2017,
            'description' => <<<TEXT
Der Erweiterungsbau besetzt eine prominente Lage am Hangfuss und ergänzt die bestehende Schulanlage zu einem klar gefassten Ensemble. Eine kompakte, viergeschossige Setzung erlaubt einen grosszügigen, zusammenhängenden Pausenplatz.

Eine vorgesetzte Pfeilerstruktur aus Sichtbeton bildet umlaufende Aussenräume aus und nimmt die topografische Setzung der Anlage auf. Im Inneren prägen helle Lernlandschaften, geölte Eichenböden und farbige Akzente eine konzentrierte und gleichzeitig wohnliche Schulatmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 19.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Selektiver Wettbewerb
Termine: 2017 (Wettbewerb)
Auftraggeber: Gemeinde Stäfa
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-12.jpg', 'fza-dummy-5.jpg', 'fza-dummy-8.jpg'],
        ],
        [
            'title' => 'Kindergarten Sonnenberg',
            'location' => 'Adliswil',
            'subtitle' => 'Erweiterung',
            'year' => 2017,
            'description' => <<<TEXT
Der bestehende Kindergarten aus den 1980er-Jahren wurde um eine zusätzliche Abteilung erweitert. Der Anbau setzt sich als eingeschossiges, in den Hang geschobenes Holzvolumen vom Bestand ab und schafft einen geschützten, gut belichteten Aussenspielbereich.

Im Inneren prägen sichtbare Holzoberflächen, ein hell pigmentierter Anhydritboden und farbige Linoleumeinbauten eine ruhige, lernfreundliche Atmosphäre. Der bestehende Kindergarten wurde gleichzeitig energetisch ertüchtigt und barrierefrei erschlossen.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 2.20 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2015–2017 (Planung und Realisierung)
Auftraggeber: Stadt Adliswil
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-6.jpg', 'fza-dummy-9.jpg'],
        ],
        [
            'title' => 'Wohnhaus Brüschhalde',
            'location' => 'Männedorf',
            'subtitle' => 'Neubau',
            'year' => 2016,
            'description' => <<<TEXT
Das Einfamilienhaus für eine vierköpfige Familie nutzt die ausgeprägte Hanglage und entwickelt sich auf drei Geschossen entlang einer zentralen, raumhaltigen Treppe. Sämtliche Wohnräume orientieren sich nach Süden und öffnen sich grosszügig zum See.

Die Materialisierung verbindet einen Sockel aus Sichtbeton mit einem darüber aufgesetzten, fein profilierten Holzbau aus heimischer Lärche. Innen prägen geöltes Eichenparkett, weiss lasierte Fichtenoberflächen und sorgfältig entworfene Einbauten den ruhigen Charakter des Hauses.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 1.65 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2014–2016 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-2.jpg', 'fza-dummy-7.jpg', 'fza-dummy-10.jpg'],
        ],
        [
            'title' => 'Gewerbeneubau Hardturmstrasse',
            'location' => 'Zürich',
            'subtitle' => 'Studienauftrag',
            'year' => 2016,
            'description' => <<<TEXT
Der vorgeschlagene Gewerbeneubau ersetzt einen Lagerschuppen am Rand des Industrieareals und bildet einen klar gefassten Auftakt zur weiteren Bebauung. Sechs Geschosse mit raumhohen Schaufenstern im Sockel und einem zurückhaltend gegliederten Obergeschossvolumen organisieren ein flexibles Gewerbeprogramm.

Die Tragstruktur aus Sichtbeton mit eingestellten Mauerwerksflächen erlaubt unterschiedlichste Belegungen. Eine durchlaufende Erschliessungsschicht entlang der Längsfassade nimmt sämtliche haustechnischen Anforderungen auf und hält die Mietflächen frei von Installationen.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 16.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienauftrag auf Einladung
Termine: 2016 (Wettbewerb)
Auftraggeber: Privat
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-8.jpg', 'fza-dummy-11.jpg'],
        ],
        [
            'title' => 'Mehrfamilienhaus Seestrasse',
            'location' => 'Horgen',
            'subtitle' => 'Sanierung',
            'year' => 2016,
            'description' => <<<TEXT
Das viergeschossige Mehrfamilienhaus aus den 1960er-Jahren wurde umfassend energetisch saniert und in seiner inneren Organisation behutsam aktualisiert. Eine vorgehängte, hinterlüftete Vorhangfassade aus eloxierten Aluminiumprofilen interpretiert den Charakter des Bestands neu.

Im Inneren wurden die Bäder und Küchen ersetzt; die ursprünglichen Eichenparkette und Holztüren wurden restauriert. Loggien wurden mit raumhohen Schiebeelementen versehen und können als wettergeschützte Erweiterung des Wohnraumes genutzt werden.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 5.40 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2014–2016 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-4.jpg', 'fza-dummy-9.jpg', 'fza-dummy-12.jpg'],
        ],
        [
            'title' => 'Schulhaus Riedhof',
            'location' => 'Zürich',
            'subtitle' => 'Umbau und Erweiterung',
            'year' => 2016,
            'description' => <<<TEXT
Das Schulhaus aus den 1930er-Jahren wurde in zwei Etappen umgebaut und um einen kompakten Anbau in Holzbauweise erweitert. Im Bestand wurden charakteristische Materialien wie Terrazzoböden, Sichtbeton und Holztäfer freigelegt und konservatorisch behandelt.

Der Anbau nimmt vier zusätzliche Klassenzimmer und eine offene Lernlandschaft auf und schliesst sich mit einer transparenten Verbindungshalle an den Bestand an. Eine zurückhaltende, vertikal gegliederte Lärchenfassade fügt sich in die parkartige Umgebung ein.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 8.90 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Studienauftrag auf Einladung (1. Rang)
Termine: 2013–2016 (Planung und Realisierung)
Auftraggeber: Stadt Zürich, Amt für Hochbauten
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-5.jpg', 'fza-dummy-10.jpg', 'fza-dummy-1.jpg'],
        ],
        [
            'title' => 'Hofstatt Sennrütiweg',
            'location' => 'Hombrechtikon',
            'subtitle' => 'Umnutzung Bauernhaus',
            'year' => 2015,
            'description' => <<<TEXT
Das ehemalige Bauernhaus aus dem 17. Jahrhundert wurde zu einem zeitgemässen Wohnhaus für eine Familie umgenutzt. In enger Zusammenarbeit mit der Denkmalpflege wurden die ortstypischen Bauformen, die Volumen und die landschaftliche Setzung erhalten.

Im Inneren wurden die historischen Holzbalkendecken, Sichtmauerwerke und Eichendielenböden freigelegt und konservatorisch behandelt. Eine eingestellte Box aus geöltem Eichenholz organisiert die zeitgenössischen Anforderungen an Sanitär- und Haustechnik und lässt die historische Hülle weitgehend unangetastet.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 1.80 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2013–2015 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-6.jpg', 'fza-dummy-11.jpg', 'fza-dummy-2.jpg'],
        ],
        [
            'title' => 'Doppelturnhalle Auenstein',
            'location' => 'Auenstein',
            'subtitle' => 'Offener Projektwettbewerb',
            'year' => 2015,
            'description' => <<<TEXT
Die neue Doppelturnhalle ergänzt die bestehende Schulanlage und nimmt mit einer leichten Hangeinfügung die Topografie des Geländes auf. Ein klar gegliedertes, zweigeschossiges Volumen organisiert die Hallen, Geräte- und Garderobenbereiche kompakt unter einem Dach.

Die Tragstruktur aus Brettschichtholzbindern überspannt stützenfrei beide Hallen und schafft eine helle, lichtdurchflutete Spielatmosphäre. Eine vertikal strukturierte Holzfassade aus heimischer Weisstanne fügt das Volumen ruhig in die landschaftliche Umgebung ein.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 8.70 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Offener Projektwettbewerb
Termine: 2015 (Wettbewerb)
Auftraggeber: Gemeinde Auenstein
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-7.jpg', 'fza-dummy-12.jpg', 'fza-dummy-3.jpg'],
        ],
        [
            'title' => 'Reihenhäuser Sonnhalde',
            'location' => 'Wallisellen',
            'subtitle' => 'Neubau',
            'year' => 2015,
            'description' => <<<TEXT
Die acht Reihenhäuser organisieren sich entlang einer schmalen, gemeinsam genutzten Wohngasse und reagieren mit einer leichten Verschiebung der Volumen auf den leicht abfallenden Geländeverlauf. Jede Einheit verfügt über einen privaten Gartenbereich und einen direkt zugänglichen Atelierraum im Sockel.

Die ortstypische Walmdachform fügt die Häuserzeile selbstverständlich in den Quartierkontext ein. Die Materialisierung mit verputzten Aussenwänden und filigran profilierten Holzfenstern verleiht der Reihe eine ruhige, kraftvolle Erscheinung.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 9.60 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Studienauftrag auf Einladung (1. Rang)
Termine: 2012–2015 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-8.jpg', 'fza-dummy-1.jpg', 'fza-dummy-4.jpg'],
        ],
        [
            'title' => 'Schulhaus Weid',
            'location' => 'Volketswil',
            'subtitle' => 'Studienauftrag 3. Rang',
            'year' => 2014,
            'description' => <<<TEXT
Der Erweiterungsbau ergänzt die bestehende Schulanlage durch ein kompaktes, dreigeschossiges Volumen mit acht Klassenzimmern und einer offenen Lernlandschaft pro Geschoss. Die Setzung am Rand des Pausenplatzes stärkt die räumliche Definition der gesamten Anlage.

Eine vorgesetzte Pfeilerstruktur aus Sichtbeton bildet umlaufende Aussenräume aus und schafft einen wettertauglichen Übergang zwischen Innen- und Aussenraum. Im Inneren prägen helle, mehrfach belichtete Lernlandschaften, geöltes Eichenparkett und farbige Akzente eine konzentrierte Schulatmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 14.80 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Studienauftrag auf Einladung (3. Rang)
Termine: 2014 (Wettbewerb)
Auftraggeber: Gemeinde Volketswil
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-9.jpg', 'fza-dummy-2.jpg', 'fza-dummy-5.jpg'],
        ],
        [
            'title' => 'Wohnsiedlung Loogarten',
            'location' => 'Zürich',
            'subtitle' => 'Sanierung Etappe 1',
            'year' => 2014,
            'description' => <<<TEXT
Die erste Etappe der Sanierung umfasst die umfassende energetische Ertüchtigung von vier Mehrfamilienhäusern aus den 1950er-Jahren. Die Eingriffe wurden so geplant, dass die Mieterschaft etappenweise und mit minimalen Unterbrüchen in den Wohnungen verbleiben konnte.

In jeder Wohnung wurden Bäder und Küchen ersetzt sowie die haustechnische Infrastruktur erneuert. Die ortstypische Klinkerfassade wurde fugenweise restauriert; die Fenster wurden in der ursprünglichen Profilierung neu in Holz-Metall-Konstruktion erstellt. Sorgfältig disponierte Loggien erweitern die Wohnflächen.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 11.20 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2012–2014 (Planung und Realisierung)
Auftraggeber: Baugenossenschaft Loogarten
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-10.jpg', 'fza-dummy-3.jpg', 'fza-dummy-6.jpg'],
        ],
        [
            'title' => 'Atelier- und Wohnhaus Stampfenbach',
            'location' => 'Zürich',
            'subtitle' => 'Umbau',
            'year' => 2014,
            'description' => <<<TEXT
Das ehemalige Atelierhaus wurde zu einem kombinierten Wohn- und Arbeitsort umgebaut. Die offene, stützenarme Struktur des Bestands erlaubt eine flexible, raumhohe Belegung der Geschosse und beherbergt heute eine Familienwohnung sowie zwei Arbeitsräume.

Eine eingestellte Box aus geöltem Eichenholz organisiert die internen Funktionen und lässt die historische Hülle mit ihren Sichtbacksteinwänden und Stahlträgern unangetastet. Die Beleuchtung wurde als sichtbares System aus mattschwarzen Schienen ausgebildet und prägt die Atmosphäre des Hauses.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 1.90 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2012–2014 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-11.jpg', 'fza-dummy-4.jpg', 'fza-dummy-7.jpg'],
        ],
        [
            'title' => 'Bibliothek Affoltern',
            'location' => 'Affoltern am Albis',
            'subtitle' => 'Projektwettbewerb 2. Rang',
            'year' => 2014,
            'description' => <<<TEXT
Die neue Bibliothek versteht sich als offener, durchlässiger Raum für die Stadtgemeinschaft. Ein zweigeschossiger, durchgehender Lese- und Aufenthaltsraum öffnet sich grosszügig zum Stadtplatz und schafft einen einladenden, urbanen Auftakt.

Die Tragstruktur aus Holz-Beton-Verbund erlaubt grosse, stützenfreie Räume und ermöglicht eine flexible Belegung. Im Inneren prägen sichtbare Holzoberflächen, geöltes Eichenparkett und mit textilen Bezügen ausgestattete Lese-Inseln eine einladende, wohnliche Atmosphäre.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 8.40 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Projektwettbewerb auf Einladung (2. Rang)
Termine: 2014 (Wettbewerb)
Auftraggeber: Stadt Affoltern am Albis
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-12.jpg', 'fza-dummy-5.jpg', 'fza-dummy-8.jpg'],
        ],
        [
            'title' => 'Stadtvilla Hottingen',
            'location' => 'Zürich',
            'subtitle' => 'Umbau und Sanierung',
            'year' => 2013,
            'description' => <<<TEXT
Die gründerzeitliche Stadtvilla wurde umfassend saniert und in ihrer inneren Organisation behutsam aktualisiert. In enger Abstimmung mit der Denkmalpflege wurden originale Stuckaturen, Riemenböden und bemalte Holzdecken freigelegt und konservatorisch gesichert.

Im Inneren wurden die ursprünglichen Raumstrukturen wiederhergestellt; spätere Einbauten wurden zurückgebaut. Eine sorgfältig abgestimmte Materialpalette aus geöltem Eichenholz, gefärbtem Zementputz und mattem Messing nimmt Bezug auf die historische Substanz und übersetzt diese in eine zurückhaltende Gegenwart.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 3.60 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2011–2013 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-1.jpg', 'fza-dummy-6.jpg', 'fza-dummy-9.jpg'],
        ],
        [
            'title' => 'Berufsschule Bülach',
            'location' => 'Bülach',
            'subtitle' => 'Selektiver Wettbewerb',
            'year' => 2013,
            'description' => <<<TEXT
Die neue Berufsschule organisiert sich als kompaktes, fünfgeschossiges Volumen am Rand des Bahnhofareals. Eine zentrale Halle erschliesst alle Geschosse und dient gleichzeitig als Aufenthalts- und Lernbereich.

Die robuste Materialisierung aus Sichtbeton, Stahlgeländern und geöltem Industrieparkett verweist auf den Werkstättencharakter der Schule und schafft langlebige, identitätsstiftende Räume. Eine fein gegliederte Vorhangfassade aus eloxierten Aluminiumprofilen verleiht dem Gebäude eine markante, aber zurückhaltende Erscheinung.
TEXT,
            'info' => <<<TEXT
Status: Wettbewerbsbeitrag
Gesamtbaukosten: CHF 24.20 Mio. (BKP 1–9, geschätzt)
Leistungen: Wettbewerbsprojekt
Auftragsart: Selektiver Wettbewerb
Termine: 2013 (Wettbewerb)
Auftraggeber: Kanton Zürich, Hochbauamt
Architektur: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-2.jpg', 'fza-dummy-7.jpg', 'fza-dummy-10.jpg'],
        ],
        [
            'title' => 'Wohn- und Geschäftshaus Limmatquai',
            'location' => 'Zürich',
            'subtitle' => 'Sanierung',
            'year' => 2013,
            'description' => <<<TEXT
Das gründerzeitliche Wohn- und Geschäftshaus an prominenter Lage am Limmatquai wurde umfassend saniert. Die Eingriffe wurden in enger Abstimmung mit der Denkmalpflege geplant und folgen dem Prinzip der zurückhaltenden Übersetzung historischer Substanz.

Im Erdgeschoss wurden die Ladenflächen behutsam reorganisiert und mit grossformatigen Schaufenstern an den heutigen Strassenraum angeschlossen. In den Obergeschossen wurden die Wohnungen ausgebaut und die ursprünglichen Stuckdecken, Eichenparkette und Türen restauriert.
TEXT,
            'info' => <<<TEXT
Status: Ausgeführt
Gesamtbaukosten: CHF 6.80 Mio. (BKP 1–9)
Leistungen: 100% Teilleistungen
Auftragsart: Direktauftrag
Termine: 2011–2013 (Planung und Realisierung)
Auftraggeber: Privat
Architektur, Baumanagement & Bauleitung: Forrer Zimmermann Architekten
TEXT,
            'images' => ['fza-dummy-3.jpg', 'fza-dummy-8.jpg', 'fza-dummy-11.jpg'],
        ],
    ];

    public function handle(): void
    {
        $topicIds = Topic::pluck('id')->toArray();

        foreach ($this->projects as $index => $data) {
            $project = Project::updateOrCreate(
                ['title' => $data['title'], 'year' => $data['year']],
                [
                    'location' => $data['location'],
                    'subtitle' => $data['subtitle'],
                    'year' => $data['year'],
                    'description' => $data['description'],
                    'info' => $this->formatInfo($data['info']),
                    'publish' => true,
                    'feature' => crc32($data['title']) % 10 < 3,
                    'sort_order' => $index,
                    'topic_id' => !empty($topicIds) ? $topicIds[array_rand($topicIds)] : null,
                ]
            );

            if ($project->media()->count() === 0) {
                foreach ($data['images'] as $sortOrder => $imageFile) {
                    $filename = $this->copyImage($imageFile);
                    if ($filename) {
                        [$width, $height] = getimagesize(storage_path('app/public/uploads/' . $filename));
                        $project->media()->create([
                            'file' => $filename,
                            'original_name' => $imageFile,
                            'mime_type' => 'image/jpeg',
                            'size' => File::size(storage_path('app/public/uploads/' . $filename)),
                            'width' => $width,
                            'height' => $height,
                            'is_teaser' => $sortOrder === 0,
                            'sort_order' => $sortOrder,
                        ]);
                    }
                }
            }
        }

        $this->info('Projects seeded.');
    }

    private function formatInfo(string $text): string
    {
        $rows = [];
        foreach (preg_split('/\r?\n/', trim($text)) as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            [$label, $value] = array_map('trim', explode(':', $line, 2));
            $rows[] = '<strong>' . e($label) . ':</strong> ' . e($value);
        }

        return '<p>' . implode(' <br>', $rows) . '</p>';
    }

    private function copyImage(string $filename): ?string
    {
        $source = storage_path('app/seed/fza-images/' . $filename);
        if (!File::exists($source)) {
            $this->warn("Image not found: {$source}");
            return null;
        }

        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $unique = Str::uuid() . '.' . $ext;
        Storage::disk('public')->put('uploads/' . $unique, File::get($source));

        return $unique;
    }
}
