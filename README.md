# Large Government of Canada IT projects

This repository powers [a website displaying Government of Canada IT projects over $1 million](https://large-government-of-canada-it-projects.github.io/).

It uses [Hugo](https://gohugo.io) and was built with templates adapted from the [OneDly Project Theme](https://github.com/cdeck3r/OneDly-Theme).

The code here is licensed under the [MIT License](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/LICENSE). 

## Source data

The data displayed on the website is [available in CSV format here](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/csv/gc-it-projects-combined.csv). You can also download individual CSV data for the [2016](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/csv/gc-it-projects-2016.csv) and [2019](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/csv/gc-it-projects-2016.csv) datasets, or a [cumulative but unindexed CSV file that includes both years of data](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/csv/gc-it-projects-cumulative.csv).

The data originated from two sessional papers, archived here from [2016](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/pdf/8555-421-266.pdf) and [2019](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/static/pdf/8555-421-2460.pdf), available from the Library of Parliament.

You might be interested in:

*   [Additional sessional papers on government IT topics](https://github.com/lchski/free-the-data/tree/master/lop/sessional-papers)
*   [Analyzing Government of Canada-wide contract spending](https://goc-spending.github.io/analysis/)

**Contribute your own data visualizations!** We’d love to list them on the homepage. You can get in touch [by Twitter](https://twitter.com/sboots) or by [creating a pull request on the content page](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/content/post/index-2.md#learn-more).

## Usage instructions

### Installing

Development of this website requires [Hugo](https://gohugo.io/getting-started/installing), [npm](https://nodejs.org/en/download/), and [php](https://www.php.net/manual/en/install.php).

After cloning the repository, run:

```
npm install
```

to install the [gh-pages](https://github.com/tschaub/gh-pages) package used for deployments.

### Local development

For local development, use Hugo's built-in server:

```
hugo server -D --disableFastRender
```

To deploy updates to GitHub pages, use:

```
npm run deploy
```

The [generated data table HTML](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/layouts/shortcodes/tabledata.html) is produced by a [small PHP script](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/_handling/update.php). If the CSV file is modified, you can update the data table HTML with:

```
php _handling/update.php
```

After updating the data table HTML (or other content), use the deploy command above to update the live website. You can change the destination GitHub project by editing [`package.json`](https://github.com/YOWCT/large-government-of-canada-it-projects/blob/master/package.json).

## An [Ottawa Civic Tech](https://ottawacivictech.ca/) project

This is a volunteer project and is not affiliated with the Government of Canada.
