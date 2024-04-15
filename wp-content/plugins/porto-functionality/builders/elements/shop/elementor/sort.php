<?php
if (!defined('ABSPATH')) {
    exit(); // Exit if accessed directly.
}

/**
 * Porto Shop Builder Sort By Widget
 *
 * @since 6.0
 */

use Elementor\Controls_Manager;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Schemes\Typography;
use Elementor\Group_Control_Typography;

class Porto_Elementor_SB_Sort_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'porto_sb_sort';
    }

    public function get_title()
    {
        return __('Ordenado por', 'porto-functionality');
    }

    public function get_categories()
    {
        return ['porto-sb'];
    }

    public function get_keywords()
    {
        return ['sort by', 'shop', 'woocommerce'];
    }

    public function get_icon()
    {
        return 'Simple-Line-Icons-arrow-down';
    }

    public function get_script_depends()
    {
        return [];
    }

    public function get_custom_help_url()
    {
        return 'https://www.portotheme.com/wordpress/porto/documentation/shop-builder-elements/';
    }

    protected function register_controls()
    {
        $right = is_rtl() ? 'left' : 'right';

        $this->start_controls_section('section_sort_label', [
            'label' => esc_html('Label', 'porto-functionality'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('label_hide', [
            'label' => esc_html__('Label Visibility', 'porto-functionality'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                '' => __('Default', 'porto-functionality'),
                'none' => __('Hide', 'porto-functionality'),
            ],
            'selectors' => [
                '.elementor-element-{{ID}} label' => 'display: {{VALUE}};',
            ],
        ]);

        $this->add_control('label_color', [
            'label' => esc_html__('Label Color', 'porto-functionality'),
            'description' => esc_html__(
                'Controls color of label.',
                'porto-functionality'
            ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '.elementor-element-{{ID}} label' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'label_typography',
            'label' => esc_html__('Label Typography', 'porto-functionality'),
            'selector' => '.elementor-element-{{ID}} label',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_sort_select', [
            'label' => esc_html('Select', 'porto-functionality'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('select_color', [
            'label' => esc_html__('Select box Color', 'porto-functionality'),
            'description' => esc_html__(
                'Controls color of select box.',
                'porto-functionality'
            ),
            'type' => Controls_Manager::COLOR,
            'selectors' => [
                '.elementor-element-{{ID}} select' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'select_typography',
            'label' => esc_html__(
                'Select box Typography',
                'porto-functionality'
            ),
            'selector' => '.elementor-element-{{ID}} select',
        ]);

        $this->add_responsive_control('select_padding', [
            'label' => esc_html__('Select box Padding', 'porto-functionality'),
            'description' => esc_html__(
                'Controls padding of select box.',
                'porto-functionality'
            ),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px', '%', 'em'],
            'selectors' => [
                '.elementor-element-{{ID}} select' =>
                    'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('spacing', [
            'label' => esc_html__('Spacing(px)', 'porto-functionality'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'step' => 1,
                    'min' => 0,
                    'max' => 20,
                ],
            ],
            'selectors' => [
                '.elementor-element-{{ID}} label' => "margin-{$right}: {{SIZE}}px",
            ],
            'description' => esc_html__(
                'Controls spacing between label and select box.',
                'porto-functionality'
            ),
        ]);

        $this->add_control('select_height', [
            'label' => esc_html__('Select Height(px)', 'porto-functionality'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => [
                    'step' => 1,
                    'min' => 0,
                    'max' => 100,
                ],
            ],
            'selectors' => [
                '.elementor-element-{{ID}} select' => 'height: {{SIZE}}px',
            ],
            'description' => esc_html__(
                'Controls height of Select Box.',
                'porto-functionality'
            ),
        ]);

        $this->end_controls_section();
    }

    protected function render()
    {
        $atts = $this->get_settings_for_display();
        include PORTO_BUILDERS_PATH . '/elements/shop/wpb/sort.php';
    }
}
