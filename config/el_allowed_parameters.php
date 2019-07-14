<?php

return [

    "tokenizer" => [
        "standard" => [
            "max_token_length"
        ],
        "letter" => [],
        "lowercase" => [],
        "whitespace" => [
            "max_token_length"
        ],
        "uax_url_email" => [
            "max_token_length"
        ],
        "classic" => [
            "max_token_length"
        ],
        "thai" => [],
        "ngram" => [
            "min_gram",
            "max_gram",
            "token_chars",
        ],
        "edge_ngram" => [
            "min_gram",
            "max_gram",
            "token_chars",
        ],
        "keyword" => [
            "buffer_size"
        ],
        "pattern" => [
            "pattern",
            "flags",
            "group"
        ],
        "char_group" => [
            "tokenize_on_chars"
        ],
        "simple_pattern" => [
            "pattern"
        ],
        "simple_pattern_split" => [
            "pattern"
        ],
        "path_hierarchy" => [
            "delimiter",
            "replacement",
            "buffer_size",
            "reverse",
            "skip"
        ]
    ],


    "filter" => [
        "asciifolding" => [
            "preserve_original"
        ],
        "flatten_graph" => [],
        "length" => [
            "min",
            "max"
        ],
        "lowercase" => [
            "language"
        ],
        "uppercase" => [],
        "nGram" => [
            "min_gram",
            "max_gram"
        ],
        "edgeNGram" => [
            "min_gram",
            "max_gram",
            "side"
        ],
        "porter_stem" => [],
        "shingle" => [
            "max_shingle_size",
            "min_shingle_size",
            "output_unigrams",
            "output_unigrams_if_no_shingles",
            "token_separator",
            "filler_token"
        ],
        "stop" => [
            "stopwords",
            "stopwords_path",
            "ignore_case",
            "remove_trailing"
        ],
        "word_delimiter" => [
            "generate_word_parts",
            "generate_number_parts",
            "catenate_words",
            "catenate_numbers",
            "catenate_all",
            "split_on_case_change",
            "preserve_original",
            "split_on_numerics",
            "stem_english_possessive",
            "protected_words",
            "type_table"
        ],
        "word_delimiter_graph" => [
            "generate_word_parts",
            "generate_number_parts",
            "catenate_words",
            "catenate_numbers",
            "catenate_all",
            "split_on_case_change",
            "preserve_original",
            "split_on_numerics",
            "stem_english_possessive",
            "protected_words",
            "type_table"
        ],
        "multiplexer" => [
            "preserve_original",
            "filters"
        ],
        "condition" => [
            "filter",
            "script"
        ],
        "predicate_token_filter" => [
            "script"
        ],
        "stemmer" => [
            "name"
        ],
        "stemmer_override" => [
            "rules",
            "rules_path"
        ],
        "keyword_marker" => [
            "keywords",
            "keywords_path",
            "keywords_pattern",
            "ignore_case"
        ],
        "keyword_repeat" => [],
        "kstem" => [],
        "snowball" => [
            "language"
        ],
        "phonetic" => [],
        "synonym" => [
            "expand",
            "lenient"
        ],
        "synonym_graph" => [
            "expand",
            "lenient",
            "synonyms"
        ],
        "hyphenation_decompounder" => [
            "word_list",
            "word_list_path",
            "hyphenation_patterns_path",
            "min_word_size",
            "min_subword_size",
            "max_subword_size",
            "only_longest_match"
        ],
        "dictionary_decompounder" => [
            "word_list",
            "word_list_path",
            "hyphenation_patterns_path",
            "min_word_size",
            "min_subword_size",
            "max_subword_size",
            "only_longest_match"
        ],
        "reverse" => [],
        "elision" => [
            "articles"
        ],
        "truncate" => [
            "length"
        ],
        "unique" => [
            "only_on_same_position"
        ],
        "pattern_capture" => [
            "preserve_original",
            "patterns"
        ],
        "pattern_replace" => [
            "pattern",
            "replacement"
        ],
        "trim" => [],
        "limit" => [
            "max_token_count",
            "consume_all_tokens"
        ],
        "hunspell" => [
            "locale",
            "dedup",
            "dictionary",
            "longest_only"
        ],
        "common_grams" => [
            "common_words",
            "common_words_path",
            "ignore_case",
            "query_mode"
        ],
        "arabic_normalization" => [],
        "german_normalization" => [],
        "hindi_normalization" => [],
        "indic_normalization" => [],
        "sorani_normalization" => [],
        "persian_normalization" => [],
        "scandinavian_folding" => [],
        "scandinavian_normalization" => [],
        "serbian_normalization" => [],
        "cjk_width" => [],
        "cjk_bigram" => [
            "ignored_scripts",
            "output_unigrams"
        ],
        "delimited_payload" => [
            "delimiter",
            "encoding"
        ],
        "keep" => [
            "keep_words",
            "keep_words_path",
            "keep_words_case"
        ],
        "keep_types" => [
            "types",
            "mode"
        ],
        "classic" => [],
        "apostrophe" => [],
        "decimal_digit" => [],
        "fingerprint" => [
            "separator",
            "max_output_size"
        ],
        "min_hash" => [
            "hash_count",
            "bucket_count",
            "hash_set_size",
            "with_rotation"
        ],
        "remove_duplicates" => [],
    ],


    "char_filter" => [
        "html_strip" => [
            "escaped_tags"
        ],
        "mapping" => [
            "mappings",
            "mappings_path"
        ],
        "pattern_replace" => [
            "pattern",
            "replacement",
            "flags"
        ]
    ],


    'analyzers' => [
        'standard' => [
            "max_token_length",
            "stopwords",
            "stopwords_path"
        ],
        'simple' => [],
        'whitespace' => [],
        'stop' => [
            "stopwords",
            "stopwords_path"
        ],
        'keyword' => [],
        'pattern' => [
            "pattern",
            "flags",
            "lowercase",
            "stopwords",
            "stopwords_path"
        ],
        'fingerprint' => [
            "separator",
            "max_output_size",
            "stopwords",
            "stopwords_path"
        ]
    ],

    "mapping" => [
        "range" => [
            "coerce",
            "boost",
            "index",
            "store"
        ],
        "boolean" => [
            "boost",
            "index",
            "store",
            "doc_values",
            "null_value"
        ],
        "date" => [
            "boost",
            "index",
            "store",
            "doc_values",
            "null_value",
            "locale",
            "ignore_malformed"
        ],
        "geo_point" => [
            "ignore_malformed",
            "ignore_z_value",
            "null_value"
        ],
        "geo_shape" => [],
        "ip" => [
            "boost",
            "index",
            "store",
            "doc_values",
            "null_value"
        ],
        "keyword" => [
            "boost",
            "index",
            "store",
            "doc_values",
            "null_value",
            "eager_global_ordinals",
            "fields",
            "ignore_above",
            "index_options",
            "norms",
            "similarity",
            "normalizer"
        ],
        "nested" => [
            "dynamic",
            "properties"
        ],
        "numeric" => [
            "coerce",
            "boost",
            "doc_values",
            "index",
            "store",
            "null_value",
            "ignore_malformed"
        ],
        "object" => [
            "dynamic",
            "enable",
            "properties"
        ],
        "text" => [
            "analyzer",
            "boost",
            "eager_global_ordinals",
            "fielddata",
            "fielddata_frequency_filter",
            "fields",
            "index",
            "index_options",
            "index_prefixes",
            "index_phrases",
            "norms",
            "position_increment_gap",
            "store",
            "search_analyzer",
            "search_quote_analyzer",
            "similarity",
            "term_vector"
        ],
    ],

    "score_mode" => [
        "total",
        "multiply",
        "avg",
        "max",
        "min"
    ],
];
