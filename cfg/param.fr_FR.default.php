<?php
/* Lozeil -- Copyright (C) No Parking 2013 - 2013 */

$param['nb_max_writings'] = "100";

//Probabilities bayesian filter
$param['comment_weight'] = "1";
$param['amount_inc_vat_weight'] = "0.3";
$param['threshold'] = "3";
$param['fisher_threshold'] = "0.4";

## définition des paramètres de la gestion des courriels automatiques
$param['email_from'] = "lozeil@noparking.net";		// adresse de l'envoi des messages automatiques ("", par défaut)
$param['email_wrap'] = "50";		// nombre de caractères avant le retour à la ligne automatique (50 - 50 caractères, par défaut)

$param['locale_timezone'] = "Europe/Paris";
$param['locale_lang'] = "fr_FR";
$param['currency'] = "&euro;";

$param['accountant_view'] = "0";