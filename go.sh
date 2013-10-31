#!/bin/bash
echo "" > log.out
rm -Rf ./sessions/*
rm -Rf kill
php loader.php
