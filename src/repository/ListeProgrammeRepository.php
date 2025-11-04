findByUser(int $id_user): array
findByName(int $id_user, string $nom): ?ListeProgramme
createList(int $id_user, string $nom): ListeProgramme
addProgrammeToList(int $id_liste, int $id_programme): bool
removeProgrammeFromList(int $id_liste, int $id_programme): bool
getProgrammes(int $id_liste): array
