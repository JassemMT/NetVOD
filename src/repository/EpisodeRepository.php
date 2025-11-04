findById(int $id_episode): ?Episode
findBySerie(int $id_serie): array

markAsWatching(int $id_user, int $id_serie): bool  // ajoute à "en cours"
