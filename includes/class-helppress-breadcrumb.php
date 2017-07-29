<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'HelpPress_Breadcrumb' ) ) {

	class HelpPress_Breadcrumb {

		protected $trail;

		public function __construct() {

			$this->trail = array();

			$this->build_trail();

		}

		public function build_trail() {

			$this->add(
				esc_html__( 'Home', 'helppress' ),
				home_url( '/' )
			);

			$this->add(
				esc_html__( 'Knowledge Base', 'helppress' ),
				helppress_get_knowledge_base_url()
			);

			if ( is_singular( 'helppress_article' ) ) {
				$article_categories = wp_get_post_terms(
					get_the_id(),
					'helppress_article_category',
					array( 'fields' => 'ids' )
				);

				if ( $article_categories[0] ) {
					$this->add_tax_tree( $article_categories[0] );
				}

				$this->add(
					get_the_title(),
					get_permalink()
				);
			}

			if ( is_tax( array( 'helppress_article_category', 'helppress_article_tag' ) ) ) {
				$this->add_tax_tree( get_queried_object_id() );
			}

			elseif ( is_search() ) {
				$this->add(
					get_search_query(),
					get_search_link()
				);
			}

		}

		public function get_trail() {

			return $this->trail;

		}

		protected function add( $label, $url ) {

			$this->trail[] = array( 'label' => $label, 'url' => $url );

		}

		protected function add_tax_tree( $term_id ) {

			if ( term_exists( $term_id ) ) {

				$term      = get_term( $term_id );
				$tree      = array( $term->term_id );
				$ancestors = get_ancestors( $term->term_id, $term->term_slug, 'taxonomy' );

				if ( $ancestors ) {
					$tree = array_merge( $ancestors, $tree );
				}

				foreach ( $tree as $term_id ) {
					$term = get_term( $term_id );

					$this->add(
						$term->name,
						get_term_link( $term )
					);
				}

			}

		}

	}

}