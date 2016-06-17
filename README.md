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
1. talks: all the info, including the tags as a comma-delimited string, and the entire transcript shoved into a blog because I couldn't get it to work as a big text field. Feel free to correct this.
2. tags: every tag gets a row with the tag and the id of the talk that is tagged with it. This is potentially useful.

The tables should have the structured pictured in images/talks.png and images/tags.png

They php scripts be run in this order:

1. gretListofTedTalks.php: generates list of TED Talk pages. Puts the links into TedTalksLinks.txt
2. readsource.php: Reads TedTalksLinks.txt, looks up each link, scrapes it, finds the link to the transcript on each page, and gets the transcript. Populates the database.
3. checklist.php: Did readsource get every page? It doesn't for me, maybe because it's sending database uopdates faster than they can be processed? Anyway, this will generate a list of pages that are in TedTalksLinks.txt but not in the database. It writes the missing links to missingurls.txt
4. readsource.php...but this time change $listOfUrls to "missingurls.txt"
5. queryTheDB: At last you have a complete-ish database of TED Talks. Write a query in this script and it will output the results as JSON in tedtalks.json.

These scripts are under an MIT open source license. Do what you want with them.

Remember, you promised not to laugh.

David Weinberger
June 17, 2016


