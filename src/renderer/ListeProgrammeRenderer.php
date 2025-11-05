<?php
declare(strict_types= 1);
namespace netvod\renderer;

use netvod\Classes\Serie;

class ListeProgrammeRenderer implements Renderer {
    public static function render(array $params = []): string {
        $lstprogramme = $params["lst"];
        $programmes = "";
        foreach ($lstprogramme as $programme) {
            try {
                $programmes .= SerieRenderer::render(["serie" => $programme]);
            } catch (\Exception $e) {
                $programmes .= ProgrammeRenderer::render(["programme" => $programme]);
            }
            
        }
        return <<<FIN
        <div class="liste-programme">
            {$programmes}
        </div>
        FIN;

    }
}