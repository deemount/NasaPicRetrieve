# NASA EPIC Image Retriever

This is a Symfony console command that fetches images from the NASA EPIC (Earth Polychromatic Imaging Camera) API for a given date and stores them in a specified target folder.

## Prerequisites

* PHP 7.4 or higher
* Composer

## Installation

1. Clone the repository or download the source code.
2. Navigate to the project directory.
3. Install the dependencies using Composer:

```bash
composer install
```

## Usage

To fetch NASA EPIC images, run the following command:

```bash
php bin/console app:fetch-nasa-epic-images [target-folder] [date]
```

* target-folder
 (required): The path to the folder where the images will be stored.

* date
 (optional): The date for which to fetch images in the format YYYY-MM-DD. 
 If not provided, the command will fetch images for the last available date.

Example:

```bash
php bin/console app:fetch-nasa-epic-images /path/to/target/folder 2024-06-11
```

This command will fetch NASA EPIC images for the date 2024-06-11 and store them in the /path/to/target/folder/2024-06-11 directory.

## How it Works

1. The command retrieves the last available date from the NASA EPIC API if the date argument is not provided.
2. It creates a subfolder within the target-folder using the provided date.
3. The command fetches the image metadata for the given date from the NASA EPIC API.
4. For each image in the metadata, it constructs the correct URL to download the actual image file.
5. The images are downloaded and saved in the dateFolder.

## Dependencies

This command uses the following Symfony components:

* symfony/console: For creating the console command.
* symfony/http-client: For making HTTP requests to the NASA EPIC API.

## Configuration

The NASA EPIC API requires an API key to access the data. The API key should be stored as an environment variable named `NASA_EPIC_API_KEY`. You can set this variable in the `.env` file in the root directory of your project.

To set the API key, open the `.env` file and add the following line, replacing `YOUR_API_KEY` with your actual NASA EPIC API key:

## To Do's

* Add Guzzle for a better perfomance while downloading the images
* Add parallel downloads
* Add throttling
* Add enhanced (actual ***only*** natural)

## Go Version

A more simplier approach in Golang is uploaded to [this repository](https://github.com/deemount/goFetchNasaEpicImage)

## License

This project is licensed under the MIT License.

Feel free to modify or expand this documentation based on your project's specific requirements or additional features you might have implemented.