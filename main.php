<?php
        header('Content-Type: text/csv; charset=utf-8');
        // download url
        $url = 'https://www.verdgattin.is/';

        // get html
        $html = file_get_contents($url);

        // copy from <script id="__NEXT_DATA__" type="application/json"> to </script>
        $script_1 = explode('<script id="__NEXT_DATA__" type="application/json">', $html);
        $script_2 = explode('</script>', $script_1[1]);
        $script = $script_2[0];
        // decode json
        $json = json_decode($script);

        // get data
        $data = $json->props->pageProps->chartData;

        $products = array();

        $store = $_GET['store'];

        $dates = array();

        // loop through data object, get image_url, name, prices
        foreach ($data as $product_obj) {
                $product = array();
                // get image_url
                $product['img'] = $product_obj->image_url;

                // get name
                $product['name'] = $product_obj->name;

                // get prices
                $prices = $product_obj->prices;

                $product_prices = $prices->$store;

                foreach ($product_prices as $date=>$price) {
                        // add date to dates array
                        if (!in_array($date, $dates))
                                $dates[] = $date;
                        // get price
                        // add price to prices array
                        $product['prices'][$date] = (float)$price;
                                  }

                $products[] = $product;
        }
        // sort dates
        sort($dates);

        $csv_header = array('Vara');
        $csv = array();

        // loop through dates
        foreach ($dates as $key => $date) {
                // add date to csv header
                $csv_header[] = $date;
        }
        foreach ($products as $product) {
                $csv_row = array();
                //replace spaces with "\ " in name
                $product['name'] = str_replace(' ', '_', $product['name']);
                $csv_row[] = $product['name'];
                foreach ($dates as $date) {
                        // add price to csv row
                        if (isset($product['prices'][$date]))
                                $csv_row[] = $product['prices'][$date];
                        else
                                $csv_row[] = '';
                }
                $csv[] = $csv_row;
        }
        $separator = "\t";
        echo implode($separator, $csv_header) . "\n";
        foreach ($csv as $csv_row) {
                echo implode($separator, $csv_row) . "\n";
        }
?>
