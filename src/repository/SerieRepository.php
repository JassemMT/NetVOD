// Catalogue
findAll(): array
findById(int $id_serie): ?Serie
findByTitle(string $titre): ?Serie

// Notes et commentaires
getAverageRating(int $id_serie): float
getComments(int $id_serie): array
addComment(int $id_user, int $id_serie, int $note, string $contenu): bool
updateComment(int $id_user, int $id_serie, int $note, string $contenu): bool
hasUserCommented(int $id_user, int $id_serie): bool
