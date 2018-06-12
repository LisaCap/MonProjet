<?php

// src/Services/proverbes.php

namespace App\Services;

class Proverbes
{
    public function getMsg()
    {
        $proverbe = [
            "Tout vient à point à qui sait attendre",
            "Quand tout va bien on peut compter sur les autres, quand tout va mal on ne peut compter que sur sa famille.",
            "Il vaut mieux se disputer avec quelqu'un d'intelligent que parler à un imbécile.",
            "Qui veut faire quelque chose trouve un moyen, qui ne veut rien faire trouve une excuse.",
            "L’imagination est plus importante que le savoir.",
            "Rare est le nombre de ceux qui regardent avec leurs propres yeux et qui éprouvent avec leur propre sensibilité."
        ];
        
        //on prend un proverbe au hasard
        $index = array_rand($proverbe); // marche uniquement avec les indices numériques, pas les indices nommés. ex : [5] => "Toto".
        
        //on renvoie le proverbe
        return $proverbe[$index];//ex : $proverbe[5]
    }
}