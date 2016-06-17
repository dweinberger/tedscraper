# tedscraper
Scrape TED Talk pages because TED closed its API :(

I am 100% amateur. Please do not laugh at me.

All of these php files are designed to be run in a browser. At least that's how I do it.

All assume that you have created a file called id.json of this form:  
{
"mysql":"path to mysql",  
"username" :"username for mysql",  
"password" : "password for mysql",  
"database": "which database"    
}

Obviously, replace the values with your real info.

The mysql database consists of two tables:

1. talks: all the info, including the tags as a comma-delimited string, and the entire transcript shoved into a blob because I couldn't get it to work as a big text field. Feel free to correct this.
2. tags: every tag gets a row with the tag and the id of the talk that is tagged with it. This is potentially useful.

The tables should have the structured pictured in images/talks.png and images/tags.png

The php scripts should be run in this order:

1. *getListofTedTalks.php*: generates list of TED Talk pages. Puts the links into tedTalksLinks.txt
2. *readsource.ph*: Reads tedTalksLinks.txt, looks up each link, scrapes it, finds the link to the transcript on each page, and gets the transcript. Populates the database.php get every page? It doesn't for me, maybe because it's sending database updates faster than they can be processed? Anyway, this will generate a list of pages that are in tedTalksLinks.txt but not in the database. It writes the missing links to missingurls.txt
4. *readsource.php*...but this time change $listOfUrls to "missingurls.txt"
5. *queryTheDB*: At last you have a complete-ish database of TED Talks. Write a query in this script and it will output the results as JSON in tedtalks.json.

You might want to dedupe the tables. Based on [this page](http://www.justin-cook.com/wp/2006/12/12/remove-duplicate-entries-rows-a-mysql-database-table/), his should dedupe the tags table, but I would first back it up because, as noted, I don't know what I'm talking about:

````CREATE TABLE new_tags as SELECT * FROM talks WHERE 1 GROUP BY tag,talkid;  
RENAME TABLE tags to tags_orig;  
RENAME TABLE new_tags to tags;````

This should work for the talks table:

````CREATE TABLE new_talks as SELECT * FROM talks WHERE 1 GROUP BY url;  
RENAME TABLE talks to talks_orig;  
RENAME TABLE new_talks to talks;````

These scripts are under an MIT open source license. Do what you want with them.

And, remember: you promised not to laugh.

David Weinberger
June 17, 2016


