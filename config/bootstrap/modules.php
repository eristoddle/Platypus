<?php
    /**
     * In this system, 'modules' are optional components of particular forms. For example, registration might have
     * an optional component that requests additional information (like handedness or field site preferences). These 
     * components would likely be able to be turned on or off on a per-league basis, but would store their data
     * alongside the normal registration data in the registration collection
     */

    use app\models\Registrations;


    Registrations::addModules(array(
        'gRank' => array(
            'description' => 'gRank registration survey module.',
            'filters' => array(
                'save' => function($self, $params, $chain) {
                    // Check league to see if grank is enabled
                    if (!isset($params['entity']->getLeague()->modules->gRank)) {
                        $enabled = false;
                    } else {
                        $enabled = true;
                    }

                    if ($enabled and isset($params['data']['gRank'])) {
                        if (isset($params['data']['gRank']['score'])) {
                            unset($params['data']['gRank']['score']);
                        }
                        
                        # Calculate the actual score, store in secondary_rank_data.grank
                        $score = \app\modules\Registrations\GRankModule::calculateScore($params['data']['gRank']['answers']);
                        if (is_numeric($score)) {
                            $params['data']['secondary_rank_data']['grank'] = $score;
                            $params['data']['gRank']['score']  = $score;
                        }
                    }

                    return $chain->next($self, $params, $chain);
                },
                'validates' => function($self, $params, $chain) {
                    $validationResult = $chain->next($self, $params, $chain);

                    extract($params);

                    // Check league to see if grank is enabled
                    if (!isset($entity->getLeague()->modules->gRank)) {
                        $enabled = false;
                    } else {
                        $enabled = true;
                    }

                    $valid = true;
                    if ($enabled) {
                        if (!isset($entity->gRank->answers)) {
                            $valid = false;
                            $error = 'gRank data missing.';
                        } else {
                            $score = \app\modules\Registrations\GRankModule::calculateScore($entity->gRank->answers->to('array'));
                            if (!is_numeric($score)) {
                                $valid = false;
                                $params['entity']->errors('gRank.score', implode(', ', $score));
                            }
                        }
                    }

                    return ($valid && $validationResult); 
                }
            )
        ),
    ));
