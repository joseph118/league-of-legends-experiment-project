# LoL API Project - Developed Late 2014
Experimental Project using League of legends API. The API is a REST service which you can access using a Key token with only one drawback. The key can only do 10 request every minute.

# Project Summary
	Users would register and submit their Summoner Name (Game Character Name).
	An administrator would approve them and the application would get all the game data linked with the Summoner Name and store the data in the database.
	All Users and guests would be able to see all the users that have been approved in an order based on their ranking.

# Cronjob
	The cronjob was used to update the database partially (keeping the data up to date) each time since the key has a limited amount of request each minute. 
