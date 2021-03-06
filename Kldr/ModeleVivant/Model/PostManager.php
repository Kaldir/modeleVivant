<?php
namespace Kldr\ModeleVivant\Model;

class PostManager extends Manager
{
// RESEARCH
	public function researchPost($keywords) {
		$db = $this->dbConnect();
        $req = $db->query('SELECT id, content, title, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_post WHERE content RLIKE "'.$keywords.'" OR title RLIKE "'.$keywords.'" ORDER BY creation_date DESC');
        $posts = $req->fetchAll();
        return $posts;
    }

// POSTS
    public function addPost($id_user, $id_category, $title, $content) {
	    $db = $this->dbConnect();
	    $req = $db->prepare('INSERT INTO mv_post(id_user, id_category, title, content, creation_date) VALUES(?, ?, ?, ?, NOW())');
        $req->execute(array($id_user, $id_category, $title, $content));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }

    public function getPost($id_post) {
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT mv_category_posts.name AS category_name, mv_post.id, id_category, title, content, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_post JOIN mv_category_posts ON id_category = mv_category_posts.id WHERE mv_post.id = ? ORDER BY creation_date DESC');
        $req->execute(array($id_post));
        $post = $req->fetch();
        return $post;
    }

/*    public function getPosts() {
        $db = $this->dbConnect();
        $req = $db->query('SELECT id, id_category, title, content, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_post ORDER BY creation_date DESC');
        $posts = $req->fetchAll();
        return $posts;
    }
*/

    public function getPostsByCategory($id_category, $page = 1) {
        $firstElement = ($page - 1) * ELEMENT_PER_PAGE; // numéro du 1er élément de la page affichée
        
		$db = $this->dbConnect();
	    $req = $db->prepare('SELECT id, id_category, title, content, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_post WHERE id_category = ? ORDER BY creation_date DESC LIMIT ?, ?');
        $req->bindValue(1, $id_category); // permet d'attribuer les valeurs dans l'ordre d'apparition des "?" de la requête
        $req->bindValue(2, $firstElement, \PDO::PARAM_INT); // $firstElement représente le premier l'élément de la page affichée
        $req->bindValue(3, ELEMENT_PER_PAGE, \PDO::PARAM_INT);
	    $req->execute();
        $posts = $req->fetchAll();
        return $posts;
    }

    public function getSliderPosts() {
        $db = $this->dbConnect();
        $req = $db->query('SELECT id, id_category, title, content, creation_date, DATE_FORMAT(creation_date, \'%d/%m/%Y (%Hh%imin%ss)\') AS creation_date_fr FROM mv_post ORDER BY creation_date DESC LIMIT 3');
        $posts = $req->fetchAll();
        return $posts;
    }

    public function editPost($id_category, $title, $content, $id_post) {
	    $db = $this->dbConnect();
	    $req = $db->prepare('UPDATE mv_post SET id_category = ?, title = ?, content = ? WHERE id = ?');
	    $req->execute(array($id_category, $title, $content, $id_post));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }

	public function deletePost($id_post) {
        $db = $this->dbConnect();
        $req = $db->prepare('DELETE FROM mv_post WHERE id = ?');
        $req->execute(array($id_post));
        if ($req->rowCount() < 1) {
            return false;
        }
        return true;
    }

    public function nbPostsByCategory($id_category) { // Compte le nombre total de billets contenu dans la bdd
        $db = $this->dbConnect();
        $req = $db->prepare('SELECT COUNT(*) FROM mv_post WHERE id_category = ?');
        $req->execute(array($id_category));
        $nbPosts = $req->fetchColumn();
        return $nbPosts;
    }
}