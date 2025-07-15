export interface AdminPerson {
  id: number;
  email: string;
  nom: string;
  prenom: string;
  numero_telephone: string;
  photo_profil?: string;
  roles: Array<'admin' | 'gardien' | 'resident'>;
  niveau_acces?: number;
  date_nomination?: string;
  adresse_logement?: string;
}
