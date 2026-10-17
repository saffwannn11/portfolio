<?php
/**
 * Elementor widget classes. Loaded only when Elementor is active.
 *
 * @package Prime
 */

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Base: builds controls from prime_widget_schemas() and renders through
 * prime_render_section(), so Elementor output matches the theme exactly.
 */
abstract class Prime_Elementor_Widget extends Widget_Base {

	/** @var string Schema key. */
	protected $prime_key = '';

	public function get_name() {
		return 'prime-' . str_replace( '_', '-', $this->prime_key );
	}

	protected function prime_schema() {
		$all = prime_widget_schemas();
		return $all[ $this->prime_key ];
	}

	public function get_title() {
		return 'Prime · ' . $this->prime_schema()['title'];
	}

	public function get_icon() {
		return $this->prime_schema()['icon'];
	}

	public function get_categories() {
		return array( 'prime' );
	}

	public function get_keywords() {
		return array( 'prime', 'recruitment', str_replace( '_', ' ', $this->prime_key ) );
	}

	public function get_style_depends() {
		return array( 'prime-main' );
	}

	public function get_script_depends() {
		return array( 'prime-main' );
	}

	/** Turn a schema field into Elementor control args. */
	protected function prime_control_args( $field, $default ) {
		$type  = $field[0];
		$label = $field[1];
		$extra = isset( $field[2] ) ? $field[2] : array();
		$args  = array( 'label' => $label );

		switch ( $type ) {
			case 'textarea':
				$args += array( 'type' => Controls_Manager::TEXTAREA, 'rows' => 4, 'dynamic' => array( 'active' => true ) );
				break;
			case 'wysiwyg':
				$args += array( 'type' => Controls_Manager::WYSIWYG, 'dynamic' => array( 'active' => true ) );
				break;
			case 'url':
				$args += array( 'type' => Controls_Manager::URL, 'dynamic' => array( 'active' => true ), 'placeholder' => 'https://' );
				$default = is_array( $default ) ? $default : array( 'url' => (string) $default );
				break;
			case 'media':
				$args += array( 'type' => Controls_Manager::MEDIA, 'dynamic' => array( 'active' => true ) );
				break;
			case 'select':
				$args += array( 'type' => Controls_Manager::SELECT, 'options' => $extra['options'] );
				if ( '' === $default ) {
					$default = key( $extra['options'] );
				}
				break;
			case 'icon':
				$args += array( 'type' => Controls_Manager::SELECT, 'options' => prime_icon_options() );
				break;
			case 'switcher':
				$args += array( 'type' => Controls_Manager::SWITCHER, 'return_value' => 'yes' );
				break;
			case 'number':
				$args += array( 'type' => Controls_Manager::NUMBER );
				if ( isset( $extra['step'] ) ) {
					$args['step'] = $extra['step'];
				}
				break;
			default:
				$args += array( 'type' => Controls_Manager::TEXT, 'label_block' => true, 'dynamic' => array( 'active' => true ) );
		}
		if ( null !== $default && '' !== $default ) {
			$args['default'] = $default;
		}
		return $args;
	}

	protected function register_controls() {
		$schema   = $this->prime_schema();
		$defaults = prime_widget_defaults( $this->prime_key );

		$this->start_controls_section( 'prime_content', array( 'label' => __( 'Content', 'prime' ) ) );
		foreach ( $schema['fields'] as $name => $field ) {
			if ( 'repeater' === $field[0] ) {
				continue;
			}
			$this->add_control( $name, $this->prime_control_args( $field, isset( $defaults[ $name ] ) ? $defaults[ $name ] : '' ) );
		}
		$this->end_controls_section();

		// Each repeater gets its own panel section.
		foreach ( $schema['fields'] as $name => $field ) {
			if ( 'repeater' !== $field[0] ) {
				continue;
			}
			$this->start_controls_section( 'prime_rep_' . $name, array( 'label' => $field[1] ) );
			$rep = new Repeater();
			foreach ( $field[2]['fields'] as $sub => $sf ) {
				$rep->add_control( $sub, $this->prime_control_args( $sf, '' ) );
			}
			$this->add_control(
				$name,
				array(
					'label'       => $field[1],
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $rep->get_controls(),
					'default'     => isset( $defaults[ $name ] ) ? $defaults[ $name ] : array(),
					'title_field' => '{{{ ' . $field[2]['title'] . ' }}}',
				)
			);
			$this->end_controls_section();
		}

		$this->prime_style_controls();
	}

	/** Shared Style tab. Theme CSS uses variables, so these restyle the whole section. */
	protected function prime_style_controls() {
		$this->start_controls_section(
			'prime_style',
			array(
				'label' => __( 'Section style', 'prime' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);
		$this->add_control(
			'prime_accent',
			array(
				'label'     => __( 'Accent colour', 'prime' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}}' => '--gold: {{VALUE}}; --gold-2: {{VALUE}}; --accent-text: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'prime_bg',
			array(
				'label'     => __( 'Background colour', 'prime' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .prime-sec' => 'background: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'prime_heading_color',
			array(
				'label'     => __( 'Heading colour', 'prime' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} .h2, {{WRAPPER}} .h3, {{WRAPPER}} .hero__title, {{WRAPPER}} .banner__title, {{WRAPPER}} .audience__title, {{WRAPPER}} h3' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_control(
			'prime_text_color',
			array(
				'label'     => __( 'Text colour', 'prime' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array( '{{WRAPPER}} p:not(.eyebrow):not(.stat__num), {{WRAPPER}} li' => 'color: {{VALUE}};' ),
			)
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'prime_heading_typo',
				'label'    => __( 'Heading typography', 'prime' ),
				'selector' => '{{WRAPPER}} .h2, {{WRAPPER}} .hero__title, {{WRAPPER}} .banner__title',
			)
		);
		$this->add_responsive_control(
			'prime_pad_top',
			array(
				'label'      => __( 'Padding top', 'prime' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 300 ) ),
				'selectors'  => array( '{{WRAPPER}} .prime-sec' => 'padding-top: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->add_responsive_control(
			'prime_pad_bottom',
			array(
				'label'      => __( 'Padding bottom', 'prime' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', 'vh' ),
				'range'      => array( 'px' => array( 'min' => 0, 'max' => 300 ) ),
				'selectors'  => array( '{{WRAPPER}} .prime-sec' => 'padding-bottom: {{SIZE}}{{UNIT}};' ),
			)
		);
		$this->end_controls_section();
	}

	protected function render() {
		prime_render_section( $this->prime_key, $this->get_settings_for_display() );
	}
}

// One class per section.
class Prime_Widget_Hero extends Prime_Elementor_Widget { protected $prime_key = 'hero'; }
class Prime_Widget_Marquee extends Prime_Elementor_Widget { protected $prime_key = 'marquee'; }
class Prime_Widget_Statement extends Prime_Elementor_Widget { protected $prime_key = 'statement'; }
class Prime_Widget_Services_Slider extends Prime_Elementor_Widget { protected $prime_key = 'services_slider'; }
class Prime_Widget_Stats extends Prime_Elementor_Widget { protected $prime_key = 'stats'; }
class Prime_Widget_Process extends Prime_Elementor_Widget { protected $prime_key = 'process'; }
class Prime_Widget_Industries_Reel extends Prime_Elementor_Widget { protected $prime_key = 'industries_reel'; }
class Prime_Widget_Why_Bento extends Prime_Elementor_Widget { protected $prime_key = 'why_bento'; }
class Prime_Widget_Testimonials extends Prime_Elementor_Widget { protected $prime_key = 'testimonials'; }
class Prime_Widget_Geo extends Prime_Elementor_Widget { protected $prime_key = 'geo'; }
class Prime_Widget_Jobs extends Prime_Elementor_Widget { protected $prime_key = 'jobs'; }
class Prime_Widget_Faq extends Prime_Elementor_Widget { protected $prime_key = 'faq'; }
class Prime_Widget_Cta_Form extends Prime_Elementor_Widget { protected $prime_key = 'cta_form'; }
class Prime_Widget_Banner extends Prime_Elementor_Widget { protected $prime_key = 'banner'; }
class Prime_Widget_Contact_Cards extends Prime_Elementor_Widget { protected $prime_key = 'contact_cards'; }
class Prime_Widget_Intro extends Prime_Elementor_Widget { protected $prime_key = 'intro'; }
class Prime_Widget_Hub_Grid extends Prime_Elementor_Widget { protected $prime_key = 'hub_grid'; }
class Prime_Widget_Features extends Prime_Elementor_Widget { protected $prime_key = 'features'; }
class Prime_Widget_Roles extends Prime_Elementor_Widget { protected $prime_key = 'roles'; }
class Prime_Widget_Timeline extends Prime_Elementor_Widget { protected $prime_key = 'timeline'; }
class Prime_Widget_Facts extends Prime_Elementor_Widget { protected $prime_key = 'facts'; }
class Prime_Widget_Gifting extends Prime_Elementor_Widget { protected $prime_key = 'gifting'; }
class Prime_Widget_Business_Card extends Prime_Elementor_Widget { protected $prime_key = 'business_card'; }
class Prime_Widget_Related extends Prime_Elementor_Widget { protected $prime_key = 'related'; }
class Prime_Widget_About_Split extends Prime_Elementor_Widget { protected $prime_key = 'about_split'; }
class Prime_Widget_Audience_Split extends Prime_Elementor_Widget { protected $prime_key = 'audience_split'; }
class Prime_Widget_Cta_Band extends Prime_Elementor_Widget { protected $prime_key = 'cta_band'; }
