<?php

namespace App\Enum;

enum ContractStatus: string
{
    case CDI = 'CDI';
    case CDD = 'CDD';
    case Interim = 'Intérimaire';
    case Freelance = 'Prestataire';
    case Apprentice = 'Apprenti';
    case Intern = 'Stagiaire';

}