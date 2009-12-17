<?php
/**
 * Lithium: the most rad php framework
 *
 * @copyright     Copyright 2009, Union of RAD (http://union-of-rad.org)
 *                Copyright 2009, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @license       http://opensource.org/licenses/mit-license.php The MIT License
 */

namespace lithium\util;

/**
 * Pluralize and singularize English words.  Inflector pluralizes and singularizes English
 * nouns. Used by Lithium's naming conventions throughout the framework.
 */
class Inflector {

	/**
	 * Contians a default map of accented and special characters to ASCII characters.  Can be
	 * extended or added to using `Inflector::rules()`.
	 *
	 * @var array
	 * @see lithium\util\Inflector::slug()
	 * @see lithium\util\Inflector::rules()
	 */
	protected static $_transliterations = array(
		'/à|á|å|â/' => 'a',
		'/è|é|ê|ẽ|ë/' => 'e',
		'/ì|í|î/' => 'i',
		'/ò|ó|ô|ø/' => 'o',
		'/ù|ú|ů|û/' => 'u',
		'/ç/' => 'c',
		'/ñ/' => 'n',
		'/ä|æ/' => 'ae',
		'/ö/' => 'oe',
		'/ü/' => 'ue',
		'/Ä/' => 'Ae',
		'/Ü/' => 'Ue',
		'/Ö/' => 'Oe',
		'/ß/' => 'ss'
	);

	/**
	 * Indexed array of words which are the same in both singular and plural form.  You can add
	 * rules to this list using `Inflector::rules()`.
	 *
	 * @var array
	 * @see lithium\util\Inflector::rules()
	 */
	protected static $_uninflected = array(
		'Amoyese', 'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus',
		'carp', 'chassis', 'clippers', 'cod', 'coitus', 'Congoese', 'contretemps', 'corps',
		'debris', 'diabetes', 'djinn', 'eland', 'elk', 'equipment', 'Faroese', 'flounder',
		'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
		'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings',
		'jackanapes', 'Kiplingese', 'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media',
		'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese', 'People',
		'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese',
		'proceedings', 'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors',
		'sea[- ]bass', 'series', 'Shavese', 'shears', 'siemens', 'species', 'swine', 'testes',
		'trousers', 'trout','tuna', 'Vermontese', 'Wenchowese', 'whiting', 'wildebeest',
		'Yengeese'
	);

	/**
	 * Contains the list of pluralization rules.
	 *
	 * @var array Contains the following keys:
	 *   - `'rules'`: An array of regular expression rules in the form of `'match' => 'replace'`,
	 *     which specify the matching and replacing rules for the pluralization of words.
	 *   - `'uninflected'`: A indexed array containing regex word patterns which do not get
	 *     inflected (i.e. singular and plural are the same).
	 *   - `'irregular'`: Contains key-value pairs of specific words which are not inflected
	 *     according to the rules. This is populated from `Inflector::$_plural` when the class
	 *     is loaded.
	 * @see lithium\util\Inflector::rules()
	 */
	protected static $_singular = array(
		'rules' => array(
			'/(s)tatuses$/i' => '\1\2tatus',
			'/^(.*)(menu)s$/i' => '\1\2',
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\1ix',
			'/(vert|ind)ices$/i' => '\1ex',
			'/^(ox)en/i' => '\1',
			'/(alias)(es)*$/i' => '\1',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
			'/(cris|ax|test)es$/i' => '\1is',
			'/(shoe)s$/i' => '\1',
			'/(o)es$/i' => '\1',
			'/ouses$/' => 'ouse',
			'/uses$/' => 'us',
			'/([m|l])ice$/i' => '\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/(m)ovies$/i' => '\1\2ovie',
			'/(s)eries$/i' => '\1\2eries',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([lr])ves$/i' => '\1f',
			'/(tive)s$/i' => '\1',
			'/(hive)s$/i' => '\1',
			'/(drive)s$/i' => '\1',
			'/([^fo])ves$/i' => '\1fe',
			'/(^analy)ses$/i' => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/([ti])a$/i' => '\1um',
			'/(p)eople$/i' => '\1\2erson',
			'/(m)en$/i' => '\1an',
			'/(c)hildren$/i' => '\1\2hild',
			'/(n)ews$/i' => '\1\2ews',
			'/^(.*us)$/' => '\\1',
			'/s$/i' => ''
		),
		'irregular' => array(),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', '.*ss'
		)
	);

	/**
	 * Contains a cache map of previously singularized words.
	 *
	 * @var array
	 */
	protected static $_singularized = array();

	/**
	 * Contains the list of pluralization rules.
	 *
	 * @var array Contains the following keys:
	 *   - `'rules'`: An array of regular expression rules in the form of `'match' => 'replace'`,
	 *     which specify the matching and replacing rules for the pluralization of words.
	 *   - `'uninflected'`: A indexed array containing regex word patterns which do not get
	 *     inflected (i.e. singular and plural are the same).
	 *   - `'irregular'`: Contains key-value pairs of specific words which are not inflected
	 *     according to the rules.
	 * @see lithium\util\Inflector::rules()
	 */
	protected static $_plural = array(
		'rules' => array(
			'/(s)tatus$/i' => '\1\2tatuses',
			'/(quiz)$/i' => '\1zes',
			'/^(ox)$/i' => '\1\2en',
			'/([m|l])ouse$/i' => '\1ice',
			'/(matr|vert|ind)(ix|ex)$/i'  => '\1ices',
			'/(x|ch|ss|sh)$/i' => '\1es',
			'/([^aeiouy]|qu)y$/i' => '\1ies',
			'/(hive)$/i' => '\1s',
			'/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
			'/sis$/i' => 'ses',
			'/([ti])um$/i' => '\1a',
			'/(p)erson$/i' => '\1eople',
			'/(m)an$/i' => '\1en',
			'/(c)hild$/i' => '\1hildren',
			'/(buffal|tomat)o$/i' => '\1\2oes',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
			'/us$/' => 'uses',
			'/(alias)$/i' => '\1es',
			'/(ax|cri|test)is$/i' => '\1es',
			'/s$/' => 's',
			'/^$/' => '',
			'/$/' => 's'
		),
		'irregular' => array(
			'atlas' => 'atlases', 'beef' => 'beefs', 'brother' => 'brothers',
			'child' => 'children', 'corpus' => 'corpuses', 'cow' => 'cows',
			'ganglion' => 'ganglions', 'genie' => 'genies', 'genus' => 'genera',
			'graffito' => 'graffiti', 'hoof' => 'hoofs', 'loaf' => 'loaves', 'man' => 'men',
			'money' => 'monies', 'mongoose' => 'mongooses', 'move' => 'moves',
			'mythos' => 'mythoi', 'numen' => 'numina', 'occiput' => 'occiputs',
			'octopus' => 'octopuses', 'opus' => 'opuses', 'ox' => 'oxen', 'penis' => 'penises',
			'person' => 'people', 'sex' => 'sexes', 'soliloquy' => 'soliloquies',
			'testis' => 'testes', 'trilby' => 'trilbys', 'turf' => 'turfs'
		),
		'uninflected' => array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep'
		)
	);

	/**
	 * Contains a cache map of previously pluralized words.
	 *
	 * @var array
	 */
	protected static $_pluralized = array();

	/**
	 * Contains a cache map of previously camelized words.
	 *
	 * @var array
	 */
	protected static $_camelized = array();

	/**
	 * Contains a cache map of previously underscored words.
	 *
	 * @var array
	 */
	protected static $_underscored = array();

	/**
	 * Contains a cache map of previously humanize words.
	 *
	 * @var array
	 */
	protected static $_humanized = array();

	/**
	 * Populates `Inflector::$_singular['irregular']` as
	 * an inversion of `Inflector::$_plural['irregular']`.
	 *
	 * @return void
	 */
	public static function __init() {
		static::$_singular['irregular'] = array_flip(static::$_plural['irregular']);
	}

	/**
	 * Gets or adds inflection and transliteration rules.
	 *
	 * @param string $type Either `'transliterations'`, `'uninflected'`, `'singular'` or `'plural'`.
	 * @param array $config
	 * @return array|void If `$config` is empty, returns the rules list specified
	 *         by `$type`, otherwise returns `null`.
	 */
	public static function rules($type, $config = array()) {
		$var = '_' . $type;

		if (!isset(static::${$var})) {
			return null;
		}

		if (empty($config)) {
			return static::${$var};
		}

		switch ($type) {
			case 'transliterations':
				$_config = array();

				foreach ($config as $key => $val) {
					if ($key[0] != '/') {
						$key = '/' . join('|', array_filter(preg_split('//u', $key))) . '/';
					}
					$_config[$key] = $val;
				}
				static::$_transliterations = array_merge(
					$_config, static::$_transliterations, $_config
				);
			break;
			case 'uninflected':
				static::$_uninflected = array_merge(static::$_uninflected, (array) $config);
				static::$_plural['regexUninflected'] = null;
				static::$_singular['regexUninflected'] = null;

				foreach ((array) $config as $word) {
					unset(static::$_singularized[$word], static::$_pluralized[$word]);
				}
			break;
			case 'singular':
			case 'plural':
				if (isset(static::${$var}[key($config)])) {
					foreach ($config as $rType => $set) {
						static::${$var}[$rType] = array_merge($set, static::${$var}[$rType], $set);

						if ($rType == 'irregular') {
							$swap = ($type == 'singular' ? '_plural' : '_singular');
							static::${$swap}[$rType] = array_flip(static::${$var}[$rType]);
						}
					}
				} else {
					static::${$var}['rules'] = array_merge(
						$config, static::${$var}['rules'], $config
					);
				}
			break;
		}
	}

	/**
	 * Changes the form of a word from singular to plural.
	 *
	 * @param string $word Word in singular form.
	 * @return string Word in plural form.
	 */
	public static function pluralize($word) {
		if (isset(static::$_pluralized[$word])) {
			return static::$_pluralized[$word];
		}
		extract(static::$_plural);

		if (!isset($regexUninflected) || !isset($regexIrregular)) {
			$regexUninflected = static::_enclose(join( '|', $uninflected + static::$_uninflected));
			$regexIrregular = static::_enclose(join( '|', array_keys($irregular)));
			static::$_plural += compact('regexUninflected', 'regexIrregular');
		}

		if (preg_match('/^(' . $regexUninflected . ')$/i', $word, $regs)) {
			return static::$_pluralized[$word] = $word;
		}

		if (preg_match('/(.*)\\b(' . $regexIrregular . ')$/i', $word, $regs)) {
			$plural = substr($word, 0, 1) . substr($irregular[strtolower($regs[2])], 1);
			static::$_pluralized[$word] = $regs[1] . $plural;
			return static::$_pluralized[$word];
		}

		foreach ($rules as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				return static::$_pluralized[$word] = preg_replace($rule, $replacement, $word);
			}
		}
		return static::$_pluralized[$word] = $word;
	}

	/**
	 * Changes the form of a word from plural to singular.
	 *
	 * @param string $word Word in plural form.
	 * @return string Word in singular form.
	 */
	public static function singularize($word) {
		if (isset(static::$_singularized[$word])) {
			return static::$_singularized[$word];
		}
		extract(static::$_singular);

		if (!isset($regexUninflected) || !isset($regexIrregular)) {
			$regexUninflected = static::_enclose(join('|', $uninflected + static::$_uninflected));
			$regexIrregular = static::_enclose(join('|', array_keys($irregular)));
			static::$_singular += compact('regexUninflected', 'regexIrregular');
		}

		if (preg_match('/(.*)\\b(' . $regexIrregular . ')$/i', $word, $regs)) {
			$singular = substr($word, 0, 1) . substr($irregular[strtolower($regs[2])], 1);
			static::$_singularized[$word] = $regs[1] . $singular;
		} elseif (preg_match('/^(' . $regexUninflected . ')$/i', $word, $regs)) {
			static::$_singularized[$word] = $word;
		} else {
			foreach ($rules as $rule => $replacement) {
				if (preg_match($rule, $word)) {
					static::$_singularized[$word] = preg_replace($rule, $replacement, $word);
					break;
				}
			}
		}

		if (!isset(static::$_singularized[$word])) {
			static::$_singularized[$word] = $word;
		}
		return static::$_singularized[$word];
	}

	/**
	 * Clears local in-memory caches.  Can be used to force a full-cache clear when updating
	 * inflection rules mid-way through request execution.
	 *
	 * @return void
	 */
	public static function clear() {
		static::$_singularized = static::$_pluralized = array();
		static::$_camelized = static::$_underscored = array();
		static::$_plural['regexUninflected'] = static::$_singular['regexUninflected'] = null;
		static::$_plural['regexIrregular'] = static::$_singular['regexIrregular'] = null;
	}

	/**
	 * Takes a underscore-syntaxed version of a word and turns it into a camel-cased one.
	 *
	 * @param string $word Underscore-syntaxed version of a word (i.e. `'red_bike'`).
	 * @return string Camel-cased version of the word (i.e. `'RedBike'`).
	 */
	public static function camelize($word) {
		if (isset(static::$_camelized[$word])) {
			return static::$_camelized[$word];
		}
		return static::$_camelized[$word] = str_replace(
			" ", "", ucwords(str_replace("_", " ", $word))
		);
	}

	/**
	 * Takes a camel-cased version of a word and turns it into an underscore-syntaxed one.
	 *
	 * @param string $word Camel-cased version of a word (i.e. `'RedBike'`).
	 * @return string Underscore-syntaxed version of the workd (i.e. `'red_bike'`).
	 */
	public static function underscore($word) {
		if (isset(static::$_underscored[$word])) {
			return static::$_underscored[$word];
		}
		return static::$_underscored[$word] = strtolower(
			preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word)
		);
	}

	/**
	 * Takes an underscore-syntaxed version of a word and turns it into an human-readable one
	 * by replacing underscores with a space, and by upper-casing the initial characters.
	 *
	 * @param string $word Underscore-syntaxed version of a word (i.e. `'red_bike'`).
	 * @param string $separator The separator character used in the initial string.
	 * @return string Human-readable version of the word (i.e. `'Red Bike'`).
	 */
	public static function humanize($word, $separator = '_') {
		if (isset(static::$_humanized[$word . $separator])) {
			return static::$_humanized[$word . $separator];
		}
		return static::$_humanized[$word . $separator] = ucwords(str_replace($separator, " ", $word));
	}

	/**
	 * Takes a camel-cased class name and returns corresponding undescore-syntaxed table name.
	 *
	 * @param string $className Name of a class (i.e. `'Post'`).
	 * @return string Name of the table (i.e. `'posts'`).
	 */
	public static function tableize($className) {
		return static::pluralize(static::underscore($className));
	}

	/**
	 * Takes a underscore-syntaxed table name and returns corresponding class name.
	 *
	 * @param string $tableName Name of a table (i.e. `'posts'`).
	 * @return string Name of a class (i.e. `'Post'`).
	 */
	public static function classify($tableName) {
		return static::camelize(static::singularize($tableName));
	}

	/**
	 * Takes a camel-cased or underscore-syntaxed variable name and returns the camel-backed
	 * version of it.
	 *
	 * @param string $string An arbitrary string (i.e. `'red_bike'`).
	 * @return string Camel-backed version of the string (i.e. `'redBike'`).
	 */
	public static function variable($string) {
		$string = static::camelize(static::underscore($string));
		$replace = strtolower(substr($string, 0, 1));
		$variable = preg_replace('/\\w/', $replace, $string, 1);
		return $variable;
	}

	/**
	 * Returns a string with all spaces converted to given replacement and
	 * non word characters removed.  Maps special characters to ASCII using
	 * `Inflector::$_transliterations`, which can be updated using `Inflector::rules()`.
	 *
	 * @param string $string An arbitrary string to convert.
	 * @param string $replacement The replacement to use for spaces.
	 * @return string The converted string.
	 * @see lithium\util\Inflector::rules()
	 */
	public static function slug($string, $replacement = '_') {
		$map = static::$_transliterations + array(
			'/[^\w\s]/' => ' ',
			'/\\s+/' => $replacement,
			str_replace(':rep', preg_quote($replacement, '/'), '/^[:rep]+|[:rep]+$/') => '',
		);
		return preg_replace(array_keys($map), array_values($map), $string);
	}

	/**
	 * Enclose a string for preg matching.
	 *
	 * @param string $string String to enclose
	 * @return string Enclosed string
	 */
	protected static function _enclose($string) {
		return '(?:' . $string . ')';
	}
}

?>