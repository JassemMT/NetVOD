<?php
declare(strict_types= 1);
namespace netvod\renderer;

class ListeProgrammeRenderer implements Renderer {
    public function render(array $params = []): string {
        $lstprogramme = $params["lst"];
        $programmes = "";
        foreach ($lstprogramme as $programme) {
            $programmes .= ProgrammeRenderer::render(["programme" => $programme]);
        }
        return <<<FIN
        <div class="liste-programme">
            {$programmes}
        </div>
        FIN;

    }
}