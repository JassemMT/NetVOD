

upload(Commentaire $c){

    $sql = "INSERT INTO Note (idSerie, note,user) 
            VALUES (:idSerie, :note, :contenu, :user)";
    $sql2 = "INSERT INTO Commentaire (idSerie, contenu,user) 
            VALUES (:idSerie, :contenu, :user)";
    

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':idSerie'   => $c->idSerie,
            ':note'         => $c->note,
            ':user'         => $c->user
        ]);

        $stmt = $pdo->prepare($sql2);
        $stmt->execute([
            ':idSerie'   => $c->idSerie,
            ':contenu'         => $c->contenu,
            ':user'         => $c->user
        ]);

    }
}
