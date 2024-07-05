<?php

namespace App\Repository\Logic;

use PHPUnit\Util\Exception;
use Psr\Log\LoggerInterface;

class ObjectChampions
{
    private string $championsMerakiURL = "https://cdn.merakianalytics.com/riot/lol/resources/latest/en-US/champions.json";

    public function __construct(private readonly LoggerInterface $logger, private readonly string $publicDir)
    {
    }

    /**
     * Fetch the champions data from Meraki.
     * @return  mixed decoded JSON champions data.
     * @throws Exception An error message.
     */
    private function championsMeraki():  mixed
    {
        $json = file_get_contents($this->championsMerakiURL);
        if ($json === FALSE){
            throw new Exception("Unable to fetch champions from Meraki. ");
        }
        $data = json_decode($json);
        if ($data === NULL) {
            throw new Exception("Unable to decode champions from Meraki. ");
        }
        return $data;
    }

    /**
     * Create a JSON file.
     * @param string $filename Name of the .json output file.
     * @param mixed $content Content of the JSON file.
     * @throws \JsonException
     */
    private function createJSON(string $filename, mixed $content ): void
    {
        $filePath = sprintf("%s/%s.json", $this->publicDir, $filename);
        $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        if (file_put_contents($filePath, $json) !== false){
            $this->logger->info('File created  successfully at ' . $filePath);
        } else {
            $this->logger->info('Failed to create the file ' . $filename);
        }
    }

    /**
     * Create championsMeraki.json in the public folder
     * @throws \JsonException
     * @throws Exception
     */
    public function createMerakiJSON(): void
    {
        $content = $this->championsMeraki();
        if($content){
            $this->createJSON("championsMeraki", $content);
        } else {
            throw new Exception("Problem accessing data from Meraki." . "\n");
        }
    }

    /**
     * From the champions Meraki, create a lighter custom JSON file with only the necessary information.
     *
     * ObjectChampions, cooldowns, images url.
     * @throws \JsonException if it couldn't create the champions.json
     * @throws Exception if there was trouble fetching data from local json.
     */
    public function championsCustom(): void
    {
        // Meraki data
        $championsMerakiPath = $this->publicDir . '/championsMeraki.json';
        $jsonMera = file_get_contents($championsMerakiPath);
        $dataMera =  json_decode($jsonMera, true);
        $filteredData = [];

        // Recharges exceptions
        $exceptRe = $this->publicDir . '/exceptionsRecharge.json';
        $jsonExceptRe = file_get_contents($exceptRe);
        $dataExceptRe = json_decode($jsonExceptRe, true);;
        $exceptReList = [];
        $i = 0;


        if(!isset($dataMera)){
            throw new Exception('Unable to fetch data from championsMeraki.json.');
        }
        foreach ($dataMera as $championName => $championData){
            $filteredData[$championName] = [
                'id' => $championData['id'],
                'key' => $championData['key'],
                'name' => $championData['name'],
                'icon' => $championData['icon'],
                'abilities' => [
                    'P' => [
                        'name' => $championData['abilities']['P']['0']['name'],
                        'icon' => $championData['abilities']['P']['0']['icon'],
                        'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                        'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                    ],
                    'Q' => [
                        'name' => $championData['abilities']['Q']['0']['name'],
                        'icon' => $championData['abilities']['Q']['0']['icon'],
                        'cooldown' => $championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                        'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                    ],
                    'W' => [
                        'name' => $championData['abilities']['W']['0']['name'],
                        'icon' => $championData['abilities']['W']['0']['icon'],
                        'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                        'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                    ],
                    'E' => [
                        'name' => $championData['abilities']['E']['0']['name'],
                        'icon' => $championData['abilities']['E']['0']['icon'],
                        'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                        'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                    ],
                    'R' => [
                            'name' => $championData['abilities']['R']['0']['name'],
                            'icon' => $championData['abilities']['R']['0']['icon'],
                            'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                            'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                    ]
                ]
            ];



            // Handle recharges exceptions if they exist for the current champion:
            $excepReExist = $dataExceptRe[$championName] ?? null;
            if ($excepReExist){
                // List of champions handled by this logic -> should match exceptionsRecharge.json
                $exceptReList[$i] = $championName;
                $i++;
                if ($dataExceptRe[$championName]['q']){
                    $filteredData[$championName]['abilities']['Q'] = [
                        'name' => $championData['abilities']['Q']['0']['name'],
                        'icon' => $championData['abilities']['Q']['0']['icon'],
                        'cooldown' => $championData['abilities']['Q']['0']['rechargeRate'],
                        'affectedByCdr' => true
                    ];
                }
                if ($dataExceptRe[$championName]['w']){
                    $filteredData[$championName]['abilities']['W'] = [
                        'name' => $championData['abilities']['W']['0']['name'],
                        'icon' => $championData['abilities']['W']['0']['icon'],
                        'cooldown' => $championData['abilities']['W']['0']['rechargeRate'],
                        'affectedByCdr' => true
                    ];
                }
                if ($dataExceptRe[$championName]['e']){
                    $filteredData[$championName]['abilities']['E'] = [
                        'name' => $championData['abilities']['E']['0']['name'],
                        'icon' => $championData['abilities']['E']['0']['icon'],
                        'cooldown' => $championData['abilities']['E']['0']['rechargeRate'],
                        'affectedByCdr' => true
                    ];
                }
                if ($dataExceptRe[$championName]['r']){
                    $filteredData[$championName]['abilities']['R'] = [
                        'name' => $championData['abilities']['R']['0']['name'],
                        'icon' => $championData['abilities']['R']['0']['icon'],
                        'cooldown' => $championData['abilities']['R']['0']['rechargeRate'],
                        'affectedByCdr' => true
                    ];
                }
            }

            // end foreach
        }

        // Uniques exceptions :
        $filteredData = $this->uniqueExceptions($filteredData, $dataMera);

        // One value exceptions :  Heimerdinger QE - Karma W - Sona QWE
        $filteredData = $this->oneValueException($filteredData, 'Heimerdinger', 'Q');
        $filteredData = $this->oneValueException($filteredData, 'Heimerdinger', 'E');
        $filteredData = $this->oneValueException($filteredData, 'Karma', 'W');
        $filteredData = $this->oneValueException($filteredData, 'Sona', 'Q');
        $filteredData = $this->oneValueException($filteredData, 'Sona', 'W');
        $filteredData = $this->oneValueException($filteredData, 'Sona', 'E');

        // Creating the final JSON for champions that will  be used to fill the Database :
        $filteredJSON = json_encode($filteredData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $championsJSONPath = $this->publicDir . '/champions.json';
        file_put_contents($championsJSONPath, $filteredJSON);
        $this->logger->info('New champions.json at ' . $championsJSONPath);
    }

    /**
     *  Function to fix the one value exceptions by duplicating this value 5 times.
     * @param $data
     * @param string $champion Champion name - need to match Meraki.
     * @param string $key Q W E R
     * @return array
     */
    private function oneValueException($data, string $champion, string $key): array{
        $value = $data[$champion]['abilities'][$key]['cooldown'][0];
        $list = array_fill(0,5, $value);
        $data[$champion]['abilities'][$key]['cooldown'] = $list;
        return $data;
    }

    /**
     *  Function to fix the unique exceptions.
     * @param $filteredData array custom json for champions.
     * @param $merakiData array Meraki data.
     * @return array
     */
    private function uniqueExceptions(array $filteredData, array $merakiData): array{
        // Aphelios
        $championData = $merakiData['Aphelios'];
        $filteredData['Aphelios'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' =>  null,
                    'affectedByCdr' =>  null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/q_calibrum.png",
                    'cooldown' => [10, 9.5, 9, 8.5, 8],
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q2' => [
                    'name' => $championData['abilities']['Q']['2']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/q_severum.png",
                    'cooldown' => [10, 9.5, 9, 8.5, 8],
                    'affectedByCdr' => $championData['abilities']['Q']['2']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q3' => [
                    'name' => $championData['abilities']['Q']['3']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/q_gravitum.png",
                    'cooldown' => [12, 11.5, 11, 10.5, 10],
                    'affectedByCdr' => $championData['abilities']['Q']['3']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q4' => [
                    'name' => $championData['abilities']['Q']['4']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/q_infernum.png",
                    'cooldown' => [9, 8, 7, 6, 6],
                    'affectedByCdr' => $championData['abilities']['Q']['4']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q5' => [
                    'name' => $championData['abilities']['Q']['5']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/aphelios/hud/icons2d/q_crescendum.png",
                    'cooldown' => [9, 8, 7, 6, 6],
                    'affectedByCdr' => $championData['abilities']['Q']['5']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ]
            ]
        ];
        // Jayce
        $championData = $merakiData['Jayce'];
        $filteredData['Jayce'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => $championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jayceq_ranged.png",
                    'cooldown' => $championData['abilities']['Q']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W1' => [
                    'name' => $championData['abilities']['W']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jaycew_ranged.png",
                    'cooldown' => $championData['abilities']['W']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E1' => [
                    'name' => $championData['abilities']['E']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/jayce/hud/icons2d/jaycee_ranged.png",
                    'cooldown' => $championData['abilities']['E']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
            ]
        ];
        $filteredData['Jayce']['abilities']['R']['cooldown'] = [6];
        // Yasuo
        $filteredData['Yasuo']['abilities']['Q']['cooldown'] = [4, 4, 4, 4, 4];
        // Yone
        $filteredData['Yone']['abilities']['Q']['cooldown'] = [4, 4, 4, 4, 4];
        $filteredData['Yone']['abilities']['W']['cooldown'] = [14, 14, 14, 14, 14];
        // Elise
        $championData = $merakiData['Elise'];
        $filteredData['Elise'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => $championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespiderq.png",
                    'cooldown' => $championData['abilities']['Q']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W1' => [
                    'name' => $championData['abilities']['W']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespiderw.png",
                    'cooldown' => $championData['abilities']['W']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E1' => [
                    'name' => $championData['abilities']['E']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/elise/hud/icons2d/elisespidere.png",
                    'cooldown' => $championData['abilities']['E']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
            ]
        ];
        // Nidalee
        $championData = $merakiData['Nidalee'];
        $filteredData['Nidalee'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => [ 6, 6, 6, 6, 6],
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => [ 12, 12, 12, 12, 12],
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => [ 3, 3, 3, 3, 3],
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_q2.png",
                    'cooldown' => [ 6, 6, 6, 6, 6],
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W1' => [
                    'name' => $championData['abilities']['W']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_w2.png",
                    'cooldown' => [ 6, 6, 6, 6, 6],
                    'affectedByCdr' => $championData['abilities']['W']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E1' => [
                    'name' => $championData['abilities']['E']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/nidalee/hud/icons2d/nidalee_e2.png",
                    'cooldown' => [ 6, 6, 6, 6, 6],
                    'affectedByCdr' => $championData['abilities']['E']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
            ]
        ];
        // Gnar
        $championData = $merakiData['Gnar'];
        $filteredData['Gnar'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => $championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/gnar/hud/icons2d/gnarbig_q.png",
                    'cooldown' => $championData['abilities']['Q']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W1' => [
                    'name' => $championData['abilities']['W']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/gnar/hud/icons2d/gnarbig_w.png",
                    'cooldown' => $championData['abilities']['W']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E1' => [
                    'name' => $championData['abilities']['E']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/gnar/hud/icons2d/gnarbig_e.png",
                    'cooldown' => $championData['abilities']['E']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
            ]
        ];
        // Reksai
        $championData = $merakiData['RekSai'];
        $filteredData['RekSai'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => $championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => $championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q1' => [
                    'name' => $championData['abilities']['Q']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/reksai/hud/icons2d/reksai_q2.png",
                    'cooldown' => $championData['abilities']['Q']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['Q']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W1' => [
                    'name' => $championData['abilities']['W']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/reksai/hud/icons2d/reksai_w2.png",
                    'cooldown' => [10, 10, 10, 10, 10],
                    'affectedByCdr' => $championData['abilities']['W']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E1' => [
                    'name' => $championData['abilities']['E']['1']['name'],
                    'icon' => "https://raw.communitydragon.org/latest/game/assets/characters/reksai/hud/icons2d/reksai_e2.png",
                    'cooldown' => $championData['abilities']['E']['1']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['1']['cooldown']['affectedByCdr'] ?? null,
                ],
            ]
        ];
        // Heimerdinger R 6 values
        $championData = $merakiData['Heimerdinger'];
        $filteredData['Heimerdinger'] = [
            'id' => $championData['id'],
            'key' => $championData['key'],
            'name' => $championData['name'],
            'icon' => $championData['icon'],
            'abilities' => [
                'P' => [
                    'name' => $championData['abilities']['P']['0']['name'],
                    'icon' => $championData['abilities']['P']['0']['icon'],
                    'cooldown' => $championData['abilities']['P']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['P']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'Q' => [
                    'name' => $championData['abilities']['Q']['0']['name'],
                    'icon' => $championData['abilities']['Q']['0']['icon'],
                    'cooldown' => [20],
                    'affectedByCdr' => $championData['abilities']['Q']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'W' => [
                    'name' => $championData['abilities']['W']['0']['name'],
                    'icon' => $championData['abilities']['W']['0']['icon'],
                    'cooldown' => $championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['W']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'E' => [
                    'name' => $championData['abilities']['E']['0']['name'],
                    'icon' => $championData['abilities']['E']['0']['icon'],
                    'cooldown' => $championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? null,
                    'affectedByCdr' => $championData['abilities']['E']['0']['cooldown']['affectedByCdr'] ?? null,
                ],
                'R' => [
                    'name' => $championData['abilities']['R']['0']['name'],
                    'icon' => $championData['abilities']['R']['0']['icon'],
                    'cooldown' => [100, 85, 70],
                    'affectedByCdr' => $championData['abilities']['R']['0']['cooldown']['affectedByCdr'] ?? null,
                ]
            ]
        ];
        // Kled Q2 lvl 1-18

        return $filteredData;
    }

    /**
     * Create an exceptions.json with all the champions that have more than one ability per spells in the Meraki data.
     *
     * In May 2024 there were 27 exceptions found for abilities & 18 for cooldowns & 20 for recharge.
     *
     * @throws \JsonException if unable to encode the json files.
     * @throws Exception if unable to fetch data from local championsMeraki.json.
     */
    public function findExceptionsMeraki(): void
    {
        $championsMerakiPath = $this->publicDir . '/championsMeraki.json';
        $json = file_get_contents($championsMerakiPath);
        $dataMera =  json_decode($json, true);

        $exceptionsListAbilities = [];
        $exceptionsListCd = [];
        $exceptionsListRecharge = [];

        if(!isset($dataMera)){
            throw new Exception('Unable to fetch data from local championsMeraki.json');
        }
        foreach ($dataMera as $championName => $championData){
            $pc = count($championData['abilities']['P']) ?? null;
            $qc = count($championData['abilities']['Q']) ?? null ;
            $wc = count($championData['abilities']['W']) ?? null ;
            $ec = count($championData['abilities']['E']) ?? null ;
            $rc = count($championData['abilities']['R']) ?? null ;

            $qd = count($championData['abilities']['Q']['0']['cooldown']['modifiers']['0']['values'] ?? [0]);
            $wd = count($championData['abilities']['W']['0']['cooldown']['modifiers']['0']['values'] ?? [0]);
            $ed = count($championData['abilities']['E']['0']['cooldown']['modifiers']['0']['values'] ?? [0]);
            $rd = count($championData['abilities']['R']['0']['cooldown']['modifiers']['0']['values'] ?? [0]);

            $qr = $championData['abilities']['Q']['0']['rechargeRate'];
            $wr = $championData['abilities']['W']['0']['rechargeRate'];
            $er = $championData['abilities']['E']['0']['rechargeRate'];
            $rr = $championData['abilities']['R']['0']['rechargeRate'];

            // Recharge rate not null means that this is the real cooldown value
            if ( $qr !== null || $wr !== null || $er !== null || $rr !== null){
                $exceptionsListRecharge[$championName] = ['q' => $qr, 'w' => $wr, 'e' => $er, 'r' => $rr];
            }

            // Each spell should have 5 or 3 cooldown values :
            if( ($qd && $qd !== 5) || ($wd && $wd !== 5) || ($ed && $ed !== 5) || ($rd && $rd !== 3) ){
                $exceptionsListCd[$championName] = ['q' => $qd, 'w' => $wd, 'e' => $ed, 'r' => $rd];
            }
            // If not no cooldowns at all :
            if (!$qd || !$wd || !$ed || !$rd){
                $exceptionsListCd[$championName] = ['q' => $qd, 'w' => $wd, 'e' => $ed, 'r' => $rd];
            }
            // Each spell should be one spell else it's an exception (Nidalee, Elise...) :
            if (($pc && $pc > 1) || ($qc && $qc > 1) || ($wc && $wc > 1) || ($ec && $ec > 1) || ($rc && $rc > 1)){
                $exceptionsListAbilities[$championName] = ['p'=> $pc, 'q'=> $qc, 'w'=> $wc, 'e'=> $ec, 'r'=> $rc];
            }
        }

        // Creating 3 JSON files with all the exceptions : Abilities, CD, Recharge :
        $filteredJSONAbilities = json_encode($exceptionsListAbilities, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $filteredJSONCd = json_encode($exceptionsListCd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $filteredJSONRecharge = json_encode($exceptionsListRecharge, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);

        $exceptionsAbilitiesJSONPath = $this->publicDir .  '/exceptionsAbilities.json';
        $exceptionsCdJSONPath = $this->publicDir .  '/exceptionsCd.json';
        $exceptionsRechargeJSONPath = $this->publicDir .  '/exceptionsRecharge.json';


        file_put_contents($exceptionsAbilitiesJSONPath, $filteredJSONAbilities);
        $this->logger->info('New exceptionsAbilities.json at ' . $exceptionsAbilitiesJSONPath);

        file_put_contents($exceptionsCdJSONPath, $filteredJSONCd);
        $this->logger->info('New exceptionsCd.json at ' . $exceptionsCdJSONPath);

        file_put_contents($exceptionsRechargeJSONPath, $filteredJSONRecharge);
        $this->logger->info('New exceptionsRecharge.json at ' . $exceptionsRechargeJSONPath);
    }

    public function getChampionsData(){
        $championsPath = $this->publicDir . '/champions.json';
        $json = file_get_contents($championsPath);
        $this->logger->info("Fetching data from the champions.json");
        return json_decode($json, true);
    }

    public function getChampionNames(): array
    {
        $championsPath = $this->publicDir . '/champions.json';
        $json = file_get_contents($championsPath);
        $data = json_decode($json, true);
        $names = [];

        foreach ($data as $champion){
            $names[]= $champion['name'];
        }
        return $names;
    }

}
