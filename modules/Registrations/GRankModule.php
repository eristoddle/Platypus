<?php

    namespace app\modules\Registrations;

    class GRankModule
    {
        protected static $_matrix = array(
            'new' => array(
                'desc' => 'I have never played Ultimate before',
                'questions' => array(
                    'level_of_play' => array(),
                    'athleticism' => array(
                        array('score' => 0.0, 'text' => 'I have never played organized sports'),
                        array('score' => 1.0, 'text' => 'I played some high school sport(s)'),
                        array('score' => 2.0, 'text' => 'I played a college scholarship sport')
                    ),
                    'ultimate_skills' => array()
                )
            ),

            'rec' => array(
                'desc' => 'Intramural, rec or church league, pickup, PE class, of military PT',
                'questions' => array(
                    'level_of_play' => array(),
                    'athleticism' => array(
                        array('score' => 0.0, 'text' => 'Athleticism is not one of my strengths'),
                        array('score' => 1.0, 'text' => 'I can keep up with most the people in pickup games'),
                        array('score' => 2.0, 'text' => 'Very few pickup players can keep up with me on offense or defense')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 0.0, 'text' => 'I just play for exercise and I don\'t worry about the rules'),
                        array('score' => 0.5, 'text' => 'I\'m learning how to cut, throw, and play defense'),
                        array('score' => 1.0, 'text' => 'I have decent skills but I am just learning about stacks offenses, marking with a force, and defending on the force side'),
                        array('score' => 2.0, 'text' => 'I have decent skills and I understand both structured offense and defense')
                    )
                )
            ),

            'hs' => array(
                'desc' => 'Organized High School team (i.e. your team has a coach and practices)',
                'questions' => array(
                    'level_of_play' => array(
                        array('score' => 2.0, 'text' => 'I was selected to the U20 National team or was a candidate'),
                        array('score' => 1.5, 'text' => 'I was selected to my region/state\'s YCC team or equivalent'),
                        array('score' => 0.5, 'text' => 'I played on a varsity high school team (Paideia, Woodward, etc.)'),
                        array('score' => 0.5, 'text' => 'I played on a team with a coach (other GHSU team)'),
                        array('score' => 0.0, 'text' => 'Gym Class'),
                        array('score' => 0.0, 'text' => 'None of the above')
                    ),
                    'athleticism' => array(
                        array('score' => 0.0, 'text' => 'Athleticism is not one of my strengths'),
                        array('score' => 1.0, 'text' => 'Average high school athlete'),
                        array('score' => 2.0, 'text' => 'Above Average High School Athlete')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 0.0, 'text' => 'I\'m just learning how to cut, throw, and play defense'),
                        array('score' => 1.0, 'text' => 'Decent at both offense and defense, but I’m still learning and improving'),
                        array('score' => 2.0, 'text' => 'I’m strong at both offense and defense'),
                        array('score' => 2.0, 'text' => 'I am an elite high school player on offense and defense')
                    )
                )
            ),
            
            'league' => array(
                'desc' => 'AFDC League or equivalent',
                'questions' => array(
                    'level_of_play' => array(),
                    'athleticism' => array(
                        array('score' => 1.0, 'text' => 'Athleticism is not one of my strengths'),
                        array('score' => 2.0, 'text' => 'Exclude club and college players in the league, I am an average league athlete'),
                        array('score' => 3.0, 'text' => 'Exclude club and college players in the league, I am an above average league athlete')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 0.0, 'text' => 'I\'m just learning how to cut, throw, and play defense'),
                        array('score' => 1.0, 'text' => 'My skills are improving but I am still learning about stack offense, marking with a force, and defending on the force side'),
                        array('score' => 2.0, 'text' => 'I have decent skills and I understand both structured offense and defense'),
                        array('score' => 2.5, 'text' => 'Exclude club and college players in the league, I am GOOD at both offense and defense'),
                        array('score' => 3.0, 'text' => 'Exclude club and college players in the league, I am VERY GOOD at both. I could play club or I have played club in the past')
                    )
                )
            ),
            
            'college' => array(
                'desc' => 'USAU registered College ultimate',
                'questions' => array(
                    'level_of_play' => array(
                        array('score' => 1.0, 'text' => 'I played at college nationals'),
                        array('score' => 0.5, 'text' => 'I played at college regionals'),
                        array('score' => 0.0, 'text' => 'None of the above')
                    ),
                    'athleticism' => array(
                        array('score' => 1.0, 'text' => 'Athleticism is not one of my strengths'),
                        array('score' => 2.0, 'text' => 'Average college athlete'),
                        array('score' => 3.0, 'text' => 'Above average college athlete'),
                        array('score' => 4.0, 'text' => 'Elite college athlete')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 1.0, 'text' => 'Still developing both offensive and defensive skills'),
                        array('score' => 2.0, 'text' => 'I mostly play defense. On offense I am a cutter and I am comfortable only throwing to open receivers'),
                        array('score' => 2.0, 'text' => 'Confident thrower/handler but I\'m not suited for person defense'),
                        array('score' => 2.5, 'text' => 'Decent at both offense and defense'),
                        array('score' => 3.0, 'text' => 'Decent at offense but I excel ("specialize") as a defensive player'),
                        array('score' => 3.0, 'text' => 'Decent at  defense but I excel ("specialize") as an offensive player'),
                        array('score' => 3.5, 'text' => 'Strong at both offense and defense'),
                        array('score' => 4.0, 'text' => 'Elite at a college level at both offense and defense')
                    )
                )
            ),
            
            'masters' => array(
                'desc' => 'Masters or grand masters club ultimate',
                'questions' => array(
                    'level_of_play' => array(
                        array('score' => 0.0, 'text' => 'Never played in the club series'),
                        array('score' => 0.5, 'text' => 'I played at grand masters nationals'),
                        array('score' => 0.5, 'text' => 'I played at masters regionals'),
                        array('score' => 1.0, 'text' => 'I played on a masters nationals team')
                    ),
                    'athleticism' => array(
                        array('score' => 2.0, 'text' => 'Athleticism is not one of my strengths'),
                        array('score' => 2.5, 'text' => 'Average masters athlete'),
                        array('score' => 3.0, 'text' => 'Above average masters athlete '),
                        array('score' => 3.5, 'text' => 'Elite masters athlete')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 1.5, 'text' => 'I mostly play defense. On offense I am a cutter and I am comfortable only throwing to open receivers'),
                        array('score' => 1.5, 'text' => 'Confident thrower/handler but I\'m not suited for person defense'),
                        array('score' => 2.0, 'text' => 'Decent at both offense and defense'),
                        array('score' => 2.0, 'text' => 'Decent at offense but I excel ("specialize") as a defensive player'),
                        array('score' => 2.0, 'text' => 'Decent at  defense but I excel ("specialize") as an offensive player'),
                        array('score' => 2.5, 'text' => 'Strong at both offense and defense'),
                        array('score' => 3.0, 'text' => 'Elite at a masters level at both offense and defense'),
                    )
                )
            ),
            
            'club' => array(
                'desc' => 'USAU registered Club ultimate',
                'questions' => array(
                    'level_of_play' => array(
                        array('score' => 0.0, 'text' => 'I have never played in the club series'),
                        array('score' => 0.0, 'text' => 'I have played at club SECTIONALS'),
                        array('score' => 0.5, 'text' => 'I have played at club REGIONALS, but didn\'t have a shot at making nationals'),
                        array('score' => 1.0, 'text' => 'I was on a team while it was a nationals contender'),
                        array('score' => 1.5, 'text' => 'I was on a team that made it to nationals but I didn\'t play a lot'),
                        array('score' => 2.0, 'text' => 'I played a considerable amount on a team at club nationals or worlds')
                    ),
                    'athleticism' => array(
                        array('score' => 3.0, 'text' => 'Average club athlete'),
                        array('score' => 3.5, 'text' => 'Above average athlete club athlete'),
                        array('score' => 4.0, 'text' => 'Elite club athlete')
                    ),
                    'ultimate_skills' => array(
                        array('score' => 1.5, 'text' => 'I mostly play defense. On offense I am a cutter and I am comfortable only throwing to open receivers'),
                        array('score' => 1.5, 'text' => 'Confident thrower/handler but I\'m not suited for person defense'),
                        array('score' => 2.0, 'text' => 'Decent at both offense and defense'),
                        array('score' => 2.0, 'text' => 'Decent at offense but I excel ("specialize") as a defensive player'),
                        array('score' => 2.0, 'text' => 'Decent at defense but I excel ("specialize") as an offensive player'),
                        array('score' => 2.5, 'text' => 'Strong at both offense and defense'),
                        array('score' => 3.0, 'text' => 'Elite at a club level at both offense and defense')
                    )
                )
            )
        );

        public static function getQuestionMatrix($experience = null)
        {
            if (!isset($experience)) {
                return self::$_matrix;
            } else {
                return isset(self::$_matrix[$experience]) ? self::$_matrix[$experience] : array();
            }
        }

        public static function getQuestionCategories($experience = null)
        {
            $experienceList = array();
            if (!isset($experience)) {
                $experienceList = array_keys(self::$_matrix);
            } else {
                $experienceList = (array) $experience;
            }

            $categoryList = array();
            foreach ($experienceList as $exp) {
                if (isset(self::$_matrix[$exp]['questions'])) {
                    foreach (array_keys(self::$_matrix[$exp]['questions']) as $c) {
                        $categoryList[$c] = true;
                    }
                }
            }

            return array_keys($categoryList);
        }

        public static function calculateScore($answers)
        {
            if (!isset($answers['experience']) or !isset(self::$_matrix[$answers['experience']])) {
                return array('Experience level not found. Did you take the survey?');
            }
            $errors = array(); $score = 0;
            $experience = $answers['experience'];



            $categoryMatrix = self::getQuestionMatrix($experience);

            foreach ($categoryMatrix['questions'] as $category => $options) {
                if (count($options) == 0) { continue; }
                if (!isset($answers[$category]) or !isset($options[$answers[$category]])) {
                    $errors[] = 'Error finding value of ' . $category . (isset($answers[$category]) ? ' for index ' . $answers[$category] . '.' : '.');
                } else {
                    $score += $options[$answers[$category]]['score'];
                }
            }

            if (count($errors) > 0) {
                return $errors;
            } else {
                return $score;
            }
        }
    }