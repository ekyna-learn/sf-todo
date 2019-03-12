ekyna-learn/sf-todo
=======

### Créer une TodoList avec Symfony.

1. Créer une entité __Task__ (tâche) :

    * content [text]
    * trashed [boolean]
    * date [datetime]

2. Développer les routes et les actions pour le contrôleur __TaskController__ : 

    | Url | Action |
    | --- | ---- |
    | / | Liste des tâches (dates décroissantes) |
    | /add | Ajouter une tâche |
    | /[id]/edit | Modifier la tâche [id] |
    | /[id]/trash | Archiver la tâche [id] |

4. Développer les pages suivantes :

    | Url | Action |
    | --- | ---- |
    | /trashed | Liste des tâches archivées (dates décroissantes) |
    | /[id]/restore | Restaurer la tâche archivée  [id] |
    | /[id]/remove | Supprimer la tâche archivée [id] |
