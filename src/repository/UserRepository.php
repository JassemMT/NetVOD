
//liste des méthodes à programmer

// Authentification
findUserByEmail(string $email): ?User  //Throws Exception
verifyCredentials(string $email, string $password): ?User
getHash(string $email) : string //Throws Exception 

createUser(string $email, string $passwordHash): User

// Gestion des listes / préférences
getUserLists(int $id_user): array
addSerieToList(int $id_user, int $id_serie, string $listName): bool
removeSerieFromList(int $id_user, int $id_serie, string $listName): bool
getFavoriteSeries(int $id_user): array
getInProgressSeries(int $id_user): array

