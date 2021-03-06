<?php
namespace Kldr\ModeleVivant\Model;

class AdManager extends Manager
{
// RESEARCH
	public function researchAd($keywords) {
		$db = $this->dbConnect();
        $req = $db->query('SELECT mv_user.mail AS user_mail, mv_user.pseudo AS user_pseudo, mv_user.avatar AS user_avatar, mv_ad.id, id_category, id_user, title, town, county, location, date_event, DATE_FORMAT(date_event, \'%d/%m/%Y\') AS date_event_fr, content, mv_ad.creation_date, DATE_FORMAT(mv_ad.creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_ad JOIN mv_user ON id_user = mv_user.id WHERE published = 1 AND (content RLIKE "'.$keywords.'" OR title RLIKE "'.$keywords.'") ORDER BY mv_ad.creation_date DESC');
        $ads = $req->fetchAll();
        return $ads;
    }

// ADVERTISEMENTS
    public function addAdvertisement($id_user, $id_category, $title, $town, $county, $location, $date_event, $content) {
	    $db = $this->dbConnect();
	    $req = $db->prepare('INSERT INTO mv_ad(id_user, id_category, title, town, county, location, date_event, content, creation_date) VALUES(?, ?, ?, ?, ?, ?, ?, ?, NOW())');
        $req->execute(array($id_user, $id_category, $title, $town, $county, $location, $date_event, $content));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }

    public function getAdvertisement($id_ad) {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT id, id_category, id_user, title, town, county, location, date_event, DATE_FORMAT(date_event, \'%d/%m/%Y\') AS date_event_fr, content, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_ad WHERE id = ? ORDER BY creation_date DESC');
        $req->execute(array($id_ad));
        $ad = $req->fetch();
        return $ad;
    }

    public function getPendingAdvertisements() {
        $db = $this->dbConnect();
        $req = $db->query('SELECT mv_user.mail AS user_mail, mv_user.pseudo AS user_pseudo, mv_user.avatar AS user_avatar, mv_ad.id, id_category, id_user, title, town, county, location, date_event, DATE_FORMAT(date_event, \'%d/%m/%Y\') AS date_event_fr, content, mv_ad.creation_date, DATE_FORMAT(mv_ad.creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_ad JOIN mv_user ON id_user = mv_user.id WHERE published = 0 ORDER BY mv_ad.creation_date DESC');
        $ads = $req->fetchAll();
        return $ads;
    }

    public function getAdvertisementsByCategory($id_category, $page = 1) {
        $firstElement = ($page - 1) * ELEMENT_PER_PAGE; // numéro du 1er élément de la page affichée

		$db = $this->dbConnect();
	    $req = $db->prepare('SELECT mv_user.mail AS user_mail, mv_user.pseudo AS user_pseudo, mv_user.avatar AS user_avatar, mv_ad.id, id_category, id_user, title, town, county, location, date_event, DATE_FORMAT(date_event, \'%d/%m/%Y\') AS date_event_fr, content, mv_ad.creation_date, DATE_FORMAT(mv_ad.creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_ad JOIN mv_user ON id_user = mv_user.id WHERE id_category = ? AND published = 1 ORDER BY mv_ad.creation_date DESC LIMIT ?, ?');
	    $req->bindValue(1, $id_category); // permet d'attribuer les valeurs dans l'ordre d'apparition des "?" de la requête
        $req->bindValue(2, $firstElement, \PDO::PARAM_INT); // $firstElement représente le premier l'élément de la page affichée
        $req->bindValue(3, ELEMENT_PER_PAGE, \PDO::PARAM_INT);
        $req->execute();
        $ads = $req->fetchAll();
        return $ads;
    }

    public function editAdvertisement($id_category, $title, $town, $county, $location, $date_event, $content, $id_ad) {
	    $db = $this->dbConnect();
	    $req = $db->prepare('UPDATE mv_ad SET id_category = ?, title = ?, town = ?, county = ?, location = ?, date_event = ?, content = ?, published = 1 WHERE id = ?');
	    $req->execute(array($id_category, $title, $town, $county, $location, $date_event, $content, $id_ad));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }

	public function deleteAdvertisement($id_ad) {
        $db = $this->dbConnect();
        $req = $db->prepare('DELETE FROM mv_ad WHERE id = ?');
        $req->execute(array($id_ad));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }	

    public function publishAdvertisement($id_ad) {
	    $db = $this->dbConnect();
		$req = $db->prepare('UPDATE mv_ad SET published = 1 WHERE id = ?');
		$req->execute(array($id_ad));
		if ($req->rowCount() < 1) {
            return false;
        }
        return true;
	}

    public function nbAdsByCategory($id_category) { // Compte le nombre total de billets contenu dans la bdd
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT COUNT(*) FROM mv_ad WHERE id_category = ?');
        $req->execute(array($id_category));
        $nbAds = $req->fetchColumn();
        return $nbAds;
    }
}