<?php

namespace App\Logic;

use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;

class Versions
{
    private string $versionLoLDDURL = "https://ddragon.leagueoflegends.com/api/versions.json";
    private string $versionLoLMerakiURL = "https://cdn.merakianalytics.com/riot/lol/resources/patches.json";
    private string $versionDDPath ;
    private string $versionMerakiPath ;

    public function __construct(private readonly LoggerInterface $logger,
                                private readonly string $publicDir,
                                private readonly string  $templatesDir)
    {
        $this->versionDDPath = $this->publicDir . '/versionDD.txt';
        $this->versionMerakiPath = $this->publicDir . '/versionMeraki.txt';
    }

    /**
     * Fetch the last patch from ddragon.
     * @return string Last ddragon patch.
     * @throws Exception An error message.
     */
    private function versionLoLDD(): string
    {
        $json = file_get_contents($this->versionLoLDDURL);

        if ($json === FALSE){
            throw new Exception("Unable to fetch version from ddragon. ");
        }
        $data = json_decode($json, true);

        if ($data === NULL) {
            throw new Exception("Unable to decode version from ddragon. ");
        }
        if (!is_array($data) || empty($data[0])) {
            throw new Exception("Version list from ddragon is missing or empty. ");
        }

        $this->logger->info('LoL version from ddragon is : ' . $data[0]);

        return $data[0];
    }

    /**
     * Fetch the last patch from Meraki
     * @return string Last Meraki patch
     * @throws Exception An error message.
     */
    private function versionLoLMeraki(): string
    {
        $json = file_get_contents($this->versionLoLMerakiURL);

        if ($json === FALSE){
            throw new Exception("Unable to fetch version from Meraki. ");
        }
        $data = json_decode($json);

        if ($data === NULL) {
            throw new Exception("Unable to decode version from Meraki. ");
        }

        $patches = $data->patches;
        $lastPatch = end($patches);

        if (isset($lastPatch->name)) {
            $lastPatchName = $lastPatch->name;
            $this->logger->info('LoL version from Meraki is : ' . $lastPatchName );
            return $lastPatchName;
        } else {
            throw new Exception("Unable to get version from Meraki. ");
        }
    }

    /**
     * Create a .txt file with the version, used to store DD & Meraki version locally.
     * @param string $filename Name of the txt file we want to create.
     * @param string $content Version we want to store.
     * @return void Will create a file in /public/ folder.
     */
    private function createVersionTxt(string $filename, string $content ): void
    {
        $filePath = sprintf("%s/%s.txt", $this->publicDir, $filename);

        if (file_put_contents($filePath, $content) !== false){
            $this->logger->info('File created  successfully at ' . $filePath );
        } else {
            $this->logger->info('Failed to create the file ' . $filePath );
        }
    }

    /**
     * Create the versionDD.txt file with the version fetch from ddragon.
     */
    private function createVersionDD(): void
    {
        $version = $this->versionLoLDD();
        $this->createVersionTxt("versionDD", $version);
    }

    /**
     * Create the versionMeraki.txt file with the version fetch from Meraki.
     */
    public function createVersionMeraki(): void
    {
        $version = $this->versionLoLMeraki();
        $this->createVersionTxt("versionMeraki", $version);

        // Also create a version.txt in templates :
        $filePath = sprintf("%s/%s.txt", $this->templatesDir, 'version');
        if (file_put_contents($filePath, $version) !== false){
            $this->logger->info('File created  successfully at ' . $filePath );
        } else {
            $this->logger->info('Failed to create the file ' . $filePath );
        }
    }

    /**
     * Check if the local ddragon version is the  same as the online version.
     * @return object { "localDD": "version", "onlineDD": "version" }
     * @throws Exception An error message.
     */
    private function checkIfDDIsUpToDate(): object
    {
    if (file_exists($this->versionDDPath)){
        $object = new \stdClass();
        $localDD = file_get_contents($this->versionDDPath);
        $onlineDD = $this->versionLoLDD();
        $object->localDD = $localDD;
        $object->onlineDD = $onlineDD;
        return $object;
    }  else {
        throw new Exception("Could not find versionDD.txt. ");
    }
    }

    /**
     * Check if the local Meraki version is the  same as the online version.
     * @return object { "localMeraki": "version", "onlineMeraki": "version" }
     * @throws Exception An error message.
     */
    private function checkIfMerakiIsUpToDate(): object
    {
        if (file_exists($this->versionMerakiPath)){
            $object = new \stdClass();
            $localMera = file_get_contents($this->versionMerakiPath);
            $onlineMera = $this->versionLoLMeraki();
            $object->localMeraki = $localMera;
            $object->onlineMeraki = $onlineMera;
            return $object;
        }  else {
            throw new Exception("Could not find versionMeraki.txt. ");
        }
    }

    /**
     * Compare online & local versions of DD & Meraki to check if an update is needed, also check if ddragon and
     * Meraki are the same version online.
     *
     * Meraki is sometimes updated 1-3 days after ddragon, and it's bad practice to not update both simultaneously.
     * @return bool False if online & local are different version and ddragon Meraki are on the same version online,
     * meaning an update is possible.
     *
     * True if the versions are similar or if the update is not possible yet.
     */
    public function compareVersionsDDMera(): bool
    {
        $objDD = $this->checkIfDDIsUpToDate();
        $objMera = $this->checkIfMerakiIsUpToDate();
        $localDD = $objDD->localDD;
        $onlineDD = $objDD->onlineDD;
        $localMera = $objMera->localMeraki;
        $onlineMera = $objMera->onlineMeraki;
        $substring = substr($onlineDD, 0, strlen($onlineMera));

        if ($substring === $onlineMera && $localDD !== $onlineDD && $localMera !== $onlineMera){
            $this->logger->info('Meraki & Dragon are similar online but not locally : an update is possible !');
            return  false;
        } else {
            if ($localDD === $onlineDD){
                $this->logger->info('Local & online versions of ddragon are similar.');
            } else {
                $this->logger->info('Local & online versions of ddragon are NOT similar.');
            }
            if ($localMera === $onlineMera) {
                $this->logger->info('Local & online versions of Meraki are similar.');
            } else {
                $this->logger->info('Local & online versions of Meraki are NOT similar.');
            }
            if ($substring === $onlineMera){
                $this->logger->info('Online versions of ddragon and Meraki are similar');
            } else {
                $this->logger->info('Online versions of ddragon and Meraki are NOT similar');
            }
            return true;
        }
    }

    /**
     * Update both local versions of ddragon & Meraki in .txt files.
     * @return void
     */
    public function updateVersions(): void
    {
        $this->createVersionDD();
        $this->createVersionMeraki();
    }
}
