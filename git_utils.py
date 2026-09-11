#Ce fichier gère toutes les interactions avec Git

import subprocess
import os

def is_git_repo():
    """Vérifie si le répertoire actuel est un dépôt Git."""
    try:
        # On utilise la commande 'git rev-parse --git-dir' pour vérifier si le répertoire actuel est un dépôt Git.
        #subprocess.run() exécute la commande et lève une exception si la commande échoue.
       subprocess.run(
           ["git", "rev-parse", "--git-dir"],
           check=True,
           capture_output=True, #Ne pas afficher la sortie de la commande
           text=True
        )
       return True
    except subprocess.CalledProcessError:
        return False
    
def get_staged_diff():
    """Récupère les différences des fichiers mis en scène (staged) par rapport à la dernière validation (commit)."""
    try:
        # On utilise la commande 'git diff --staged' pour obtenir les différences des fichiers mis en scène.
        result = subprocess.run(
            ["git", "diff", "--staged"],
            check=True,
            capture_output=True, #Ne pas afficher la sortie de la commande
            text=True
        )
        # On récupère le texte des différences à partir de la sortie standard (stdout) de la commande.
        diff_text = result.stdout
        
        if not diff_text:
            print("Aucun fichier staged. Fais un 'git add' pour mettre en scène les fichiers que tu veux valider.")
            return None
        
        return diff_text
    except subprocess.CalledProcessError as e:
        print(f"Erreur Git : {e}")
        return None
    
def get_recent_commits(n=5):
    """Récupère les n derniers commits du dépôt Git."""
    try:
        # On utilise la commande 'git log' pour obtenir les n derniers commits.
        result = subprocess.run(
            ["git", "log", "-n", str(n), "--pretty=format:%h - %s (%ci)"],
            check=True,
            capture_output=True, #Ne pas afficher la sortie de la commande
            text=True
        )
        return result.stdout.split('\n')  # Retourne la liste des commits
    
    except subprocess.CalledProcessError as e:
        print(f"Erreur Git : {e}")
        return None
    
#petit test rapide pour vérifier si le script fonctionne correctement
if __name__ == "__main__":
    if is_git_repo():
        print("Ceci est un dépôt Git.")
        diff = get_staged_diff()
        if diff:
            print("Différences des fichiers staged :")
            print(diff)
        commits = get_recent_commits()
        if commits:
            print("Derniers commits :")
            for commit in commits:
                print(commit)
    else:
        print("Ceci n'est pas un dépôt Git.")