<?php

class ControllerReportProfitStats extends Controller
{
    private $error = array();

    public function revenue()
    {

        $this->load->language('report/profit_stats_revenue');
        $this->load->model('report/profit_stats');
        $this->document->setTitle($this->language->get('heading_title'));

        $data = [];

        $data = $this->loadCommonData($data);

        $today = $this->model_report_profit_stats->getRevenueStats('today');
        $week = $this->model_report_profit_stats->getRevenueStats('week');
        $month = $this->model_report_profit_stats->getRevenueStats('month');
        $year = $this->model_report_profit_stats->getRevenueStats('year');



        $data['revenue_today']  = $this->currency->format($today['revenue'], $this->config->get('config_currency'));
        $data['orders_today']   = $today['orders'];
        $data['average_today']      = $this->currency->format($today['aov'], $this->config->get('config_currency'));

        $data['revenue_week']  = $this->currency->format($week['revenue'], $this->config->get('config_currency'));
        $data['orders_week']   = $week['orders'];
        $data['average_week']      = $this->currency->format($week['aov'], $this->config->get('config_currency'));

        $data['revenue_month']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['orders_month']   = $month['orders'];
        $data['average_month']      = $this->currency->format($month['aov'], $this->config->get('config_currency'));

        $data['revenue_year']  = $this->currency->format($year['revenue'], $this->config->get('config_currency'));
        $data['orders_year']   = $year['orders'];
        $data['average_year']      = $this->currency->format($year['aov'], $this->config->get('config_currency'));

        $data['revenue_total'] = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['orders_total'] = $month['orders'];
        $data['average_total'] = $this->currency->format($month['aov'], $this->config->get('config_currency'));

        $data['net_today'] = $this->currency->format($today['revenue'], $this->config->get('config_currency'));
        $data['net_week'] = $this->currency->format($week['revenue'], $this->config->get('config_currency'));
        $data['net_month'] = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['net_year'] = $this->currency->format($year['revenue'], $this->config->get('config_currency'));
        $data['net_total'] = $this->currency->format($month['revenue'], $this->config->get('config_currency'));

        $chart = $this->model_report_profit_stats->getStatsDataChart();

        $data['stats'] = [
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'year' => $year,
            'chart' => $chart
        ];



        $this->response->setOutput($this->load->view('report/revenue', $data));
    }

    public function profit()
    {
        $this->load->language('report/profit_stats_profit');
        $this->load->model('report/profit_stats');
        $this->document->setTitle($this->language->get('heading_title'));

        $data = [];

        $data = $this->loadCommonData($data);


        $today = $this->model_report_profit_stats->getProfitStats('today');
        $week = $this->model_report_profit_stats->getProfitStats('week');
        $month = $this->model_report_profit_stats->getProfitStats('month');
        $year = $this->model_report_profit_stats->getProfitStats('year');

        $data['revenue_today']  = $this->currency->format($today['revenue'], $this->config->get('config_currency'));
        $data['revenue_week']   = $this->currency->format($week['revenue'], $this->config->get('config_currency'));
        $data['revenue_month']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['revenue_year']   = $this->currency->format($year['revenue'], $this->config->get('config_currency'));
        $data['revenue_total']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));

        $data['cost_today']  = $this->currency->format($today['cost'], $this->config->get('config_currency'));
        $data['cost_week']   = $this->currency->format($week['cost'], $this->config->get('config_currency'));
        $data['cost_month']  = $this->currency->format($month['cost'], $this->config->get('config_currency'));
        $data['cost_year']   = $this->currency->format($year['cost'], $this->config->get('config_currency'));
        $data['cost_total']  = $this->currency->format($month['cost'], $this->config->get('config_currency'));

        $data['profit_today']  = $this->currency->format($today['profit'], $this->config->get('config_currency'));
        $data['profit_week']   = $this->currency->format($week['profit'], $this->config->get('config_currency'));
        $data['profit_month']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));
        $data['profit_year']   = $this->currency->format($year['profit'], $this->config->get('config_currency'));
        $data['profit_total']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));

        $data['margin_today']  = ($today['revenue'] > 0) ? round(($today['profit'] / $today['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_week']   = ($week['revenue'] > 0) ? round(($week['profit'] / $week['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_month']  = ($month['revenue'] > 0) ? round(($month['profit'] / $month['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_year']   = ($year['revenue'] > 0) ? round(($year['profit'] / $year['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_total']  = ($month['revenue'] > 0) ? round(($month['profit'] / $month['revenue']) * 100, 2) . '%' : '0%';
        $data['average_margin']  = ($month['revenue'] > 0) ? round(($month['profit'] / $month['revenue']) * 100, 2) . '%' : '0%';

        $data['net_profit_today']  = $this->currency->format($today['profit'], $this->config->get('config_currency'));
        $data['net_profit_week']   = $this->currency->format($week['profit'], $this->config->get('config_currency'));
        $data['net_profit_month']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));
        $data['net_profit_year']   = $this->currency->format($year['profit'], $this->config->get('config_currency'));
        $data['net_profit_total']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));

        $data['average_profit_per_order'] = ($month['orders'] > 0) ? $this->currency->format($month['profit'] / $month['orders'], $this->config->get('config_currency')) : $this->currency->format(0, $this->config->get('config_currency'));

        $chart = $this->model_report_profit_stats->getStatsDataChart();
        $data['stats'] = [
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'year' => $year,
            'chart' => $chart
        ];

        $this->response->setOutput($this->load->view('report/profit', $data));
    }

    public function orders()
    {
        $this->load->language('report/profit_stats_orders');
        $this->load->model('report/profit_stats');
        $this->document->setTitle($this->language->get('heading_title'));

        $data = [];

        $data = $this->loadCommonData($data);

        $today = $this->model_report_profit_stats->getOrdersStats('today');
        $week = $this->model_report_profit_stats->getOrdersStats('week');
        $month = $this->model_report_profit_stats->getOrdersStats('month');
        $year = $this->model_report_profit_stats->getOrdersStats('year');

        $data['orders_today']   = $today['orders'];
        $data['orders_week']    = $week['orders'];
        $data['orders_month']   = $month['orders'];
        $data['orders_year']    = $year['orders'];

        $data['revenue_today']  = $this->currency->format($today['revenue'], $this->config->get('config_currency'));
        $data['revenue_week']   = $this->currency->format($week['revenue'], $this->config->get('config_currency'));
        $data['revenue_month']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['revenue_year']   = $this->currency->format($year['revenue'], $this->config->get('config_currency'));
        $data['revenue_total']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));

        $data['average_order_value_today']  = $this->currency->format($today['average_order_value'], $this->config->get('config_currency'));
        $data['average_order_value_week']   = $this->currency->format($week['average_order_value'], $this->config->get('config_currency'));
        $data['average_order_value_month']  = $this->currency->format($month['average_order_value'], $this->config->get('config_currency'));
        $data['average_order_value_year']   = $this->currency->format($year['average_order_value'], $this->config->get('config_currency'));
        $data['average_order_value_total']  = $this->currency->format($month['average_order_value'], $this->config->get('config_currency'));

        $data['items_sold_today']  = round($today['items_sold']);
        $data['items_sold_week']   = round($week['items_sold']);
        $data['items_sold_month']  = round($month['items_sold']);
        $data['items_sold_year']   = round($year['items_sold']);
        $data['items_sold_total']  = round($month['items_sold']);

        $data['orders_pending_today']  = $today['orders_pending'];
        $data['orders_pending_month']  = $month['orders_pending'];
        $data['orders_pending_total']  = $year['orders_pending'];
      
        $data['orders_processing_today']  = $today['orders_processing'];
        $data['orders_processing_month']  = $month['orders_processing'];
        $data['orders_processing_total']  = $year['orders_processing'];

        $data['orders_completed_today']  = $today['orders_completed'];
        $data['orders_completed_month']  = $month['orders_completed'];
        $data['orders_completed_total']  = $year['orders_completed'];

        $data['orders_cancelled_today']  = $today['orders_cancelled'];
        $data['orders_cancelled_month']  = $month['orders_cancelled'];
        $data['orders_cancelled_total']  = $year['orders_cancelled'];

        $data['orders_refunded_today']  = $today['orders_refunded'];
        $data['orders_refunded_month']  = $month['orders_refunded'];
        $data['orders_refunded_total']  = $year['orders_refunded'];

        $data['average_order_value'] = $this->currency->format($year['average_order_value'], $this->config->get('config_currency'));
        $data['average_orders'] = ($month['orders'] > 0) ? round($month['orders'] / date('j'), 2) : 0;



        $chart = $this->model_report_profit_stats->getStatsDataChart();
        $data['stats'] = [
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'year' => $year,
            'chart' => $chart
        ];

        $this->response->setOutput($this->load->view('report/orders', $data));
    }

    public function margin(){


        $this->load->language('report/profit_stats_margin');
        $this->load->model('report/profit_stats');
        $this->document->setTitle($this->language->get('heading_title'));

        $data = [];

        $data = $this->loadCommonData($data);

        $today = $this->model_report_profit_stats->getMarginStats('today');
        $week = $this->model_report_profit_stats->getMarginStats('week');
        $month = $this->model_report_profit_stats->getMarginStats('month');
        $year = $this->model_report_profit_stats->getMarginStats('year');

        $data['revenue_today']  = $this->currency->format($today['revenue'], $this->config->get('config_currency'));
        $data['revenue_week']   = $this->currency->format($week['revenue'], $this->config->get('config_currency'));
        $data['revenue_month']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));
        $data['revenue_year']   = $this->currency->format($year['revenue'], $this->config->get('config_currency'));
        $data['revenue_total']  = $this->currency->format($month['revenue'], $this->config->get('config_currency'));

        $data['cost_today']  = $this->currency->format($today['cost'], $this->config->get('config_currency'));
        $data['cost_week']   = $this->currency->format($week['cost'], $this->config->get('config_currency'));
        $data['cost_month']  = $this->currency->format($month['cost'], $this->config->get('config_currency'));
        $data['cost_year']   = $this->currency->format($year['cost'], $this->config->get('config_currency'));
        $data['cost_total']  = $this->currency->format($month['cost'], $this->config->get('config_currency'));

        $data['profit_today']  = $this->currency->format($today['profit'], $this->config->get('config_currency'));
        $data['profit_week']   = $this->currency->format($week['profit'], $this->config->get('config_currency'));
        $data['profit_month']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));
        $data['profit_year']   = $this->currency->format($year['profit'], $this->config->get('config_currency'));
        $data['profit_total']  = $this->currency->format($month['profit'], $this->config->get('config_currency'));

        $data['margin_today']  = ($today['revenue'] > 0) ? round(($today['profit'] / $today['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_week']   = ($week['revenue'] > 0) ? round(($week['profit'] / $week['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_month']  = ($month['revenue'] > 0) ? round(($month['profit'] / $month['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_year']   = ($year['revenue'] > 0) ? round(($year['profit'] / $year['revenue']) * 100, 2) . '%' : '0%';
        $data['margin_total']  = ($month['revenue'] > 0) ? round(($month['profit'] / $month['revenue']) * 100, 2) . '%' : '0%';
        
        $data['margin_year']  = ($year['revenue'] > 0) ? round(($year['profit'] / $year['revenue']) * 100, 2) . '%' : '0%';

       


        $chart = $this->model_report_profit_stats->getStatsDataChart();
        $data['stats'] = [
            'today' => $today,
            'week' => $week,
            'month' => $month,
            'year' => $year,
            'chart' => $chart
        ];

        $this->response->setOutput($this->load->view('report/margin', $data));
    }

    public function loadCommonData($data){


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('report/profit_stats', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->request->get['code'])) {
            $data['code'] = $this->request->get['code'];
        } else {
            $data['code'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        return $data;
    }
}
