<?php
    const SERVER="localhost";
    const DB="prueba";
    const USER="root";
    const PASS="";

    const SGBD="mysql:host=".SERVER.";dbname=".DB;

    const METHOD="AES-256-CBC";
    //una vez que ya hay registros en la DB, no cambiar las claves
    //SECRET_KEY puede ser cualquier cosa, hasta simbolos
    const SECRET_KEY='$BP@2017';
    //SECRET_IV debe ser un numero
    const SECRET_IV="201712";