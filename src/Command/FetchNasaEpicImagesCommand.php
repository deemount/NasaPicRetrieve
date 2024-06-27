<?php

// src/Command/FetchNasaEpicImagesCommand.php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;

#[AsCommand(
    name: 'app:fetch-nasa-epic-images',
    description: 'Fetches images from Nasa Epic API',
    hidden: false,
    aliases: ['app:nasa-pic-retrieve']
)]
class FetchNasaEpicImagesCommand extends Command
{

    // This method sets up the command date and target-folder and its arguments
    protected function configure(): void
    {
        $this
            ->setDescription('Fetches images from the NASA EPIC API for a given date and stores them in a specified target folder.')
            ->addArgument('target-folder', InputArgument::REQUIRED, 'The target folder where the images will be stored.')
            ->addArgument('date', InputArgument::OPTIONAL, 'The date for which to fetch images (YYYY-MM-DD). If not provided, the last available date will be used.');
    }

    // The execute method is called when the command is executed
    // It retrieves the date and target-folder arguments from the input
    // It fetches the images for the specified date and stores them in the target folder
    // The output is written to the console
    // The method returns a success code if the execution is successful
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $targetFolder = $input->getArgument('target-folder');
        $date = $input->getArgument('date') ?: $this->getLastAvailableDate();

        $output->writeln("Fetching NASA EPIC images for date: $date");

        $dateFolder = $this->createDateFolder($targetFolder, $date);
        $images = $this->fetchImagesForDate($date);

        foreach ($images as $image) {
            $this->downloadImage($image['image'], $dateFolder);
        }

        $output->writeln("Images saved to: $dateFolder");

        return Command::SUCCESS;
    }

    private function getLastAvailableDate(): string
    {
        $client = HttpClient::create();
        $apiKey = $_ENV['NASA_EPIC_API_KEY'];
    
        $response = $client->request('GET', "https://epic.gsfc.nasa.gov/api/natural/available?api_key=$apiKey");
        $data = $response->toArray();
    
        if (is_array($data) && !empty($data)) {
            $lastAvailableDate = end($data);
        } else {
            throw new \Exception('Unable to retrieve last available date from the API response.');
        }
    
        return $lastAvailableDate;
    }

    private function createDateFolder(string $targetFolder, string $date): string
    {
        $dateFolder = $targetFolder . '/' . $date;
        if (!file_exists($dateFolder)) {
            mkdir($dateFolder, 0755, true);
        }
        return $dateFolder;
    }

    private function fetchImagesForDate(string $date): array
    {
        $client = HttpClient::create();
        $apiKey = $_ENV['NASA_EPIC_API_KEY'];

        $response = $client->request('GET', "https://epic.gsfc.nasa.gov/api/natural/date/$date?api_key=$apiKey");
        $data = $response->toArray();

        $images = [];
        foreach ($data as $imageData) {
            $imageFileName = $imageData['image'];
            $year = substr($date, 0, 4);
            $month = substr($date, 5, 2);
            $day = substr($date, 8, 2);
    
            $imageUrl = "https://api.nasa.gov/EPIC/archive/natural/$year/$month/$day/png/$imageFileName.png?api_key=$apiKey";
    
            $images[] = [
                'image' => $imageUrl,
                // Add any other relevant metadata from the API response
            ];
        }
    
        return $images;
    }

    private function downloadImage(string $imageUrl, string $targetFolder): void
    {
        $client = HttpClient::create();
        $response = $client->request('GET', $imageUrl);
    
        $parsedUrl = parse_url($imageUrl);
        $fileName = basename($parsedUrl['path']);
        $imagePath = $targetFolder . DIRECTORY_SEPARATOR . $fileName;
    
        file_put_contents($imagePath, $response->getContent(false));
    }
}
