export interface InvitePerson {
  id: number;
  email: string;
  nom: string;
  prenom: string;
  numero_telephone: string;
  photo_profil?: string;
  actif: boolean;
  date_inscription?: string;
  date_expiration?: string;
  invite_par?: string;
  commentaire?: string;
}
