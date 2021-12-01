<?php
    require_once('sml_variaveis_globais.php');
    require_once('sml_funcoes_globais.php');
    require_once('sml_conecta_banco_dados.php');

    $curDir = getcwd(). '\\data\\';

    $json = file_get_contents($curDir . 'data_trade_parte_02.csv');

    if ( $json === false )
    {
        echo "error 01";
    }
    else
    {
        $json = str_replace("\r\n", "\n", $json);
        $json = str_replace("\n\r", "\n", $json);
        $json = str_replace("\r", "\n", $json);
        $linhas = explode("\n", $json);
        $len=count($linhas);
        for ($i=0;$i<$len;$i++)
        {
            $linha = explode(";", $linhas[$i]);
            //---- https://api.binance.com/api/v3/klines?interval=1m&limit=500&startTime=1638241459000&endTime=1638241519000&symbol=BNBUSDT
            if(substr($linhas[$i],0,4) === '2021')
            {
                //---- echo $linhas[$i];
                //---- echo'</br>';

                //2021-11-30 00:04:19;HIVEBUSD;BUY;3,04780000;188,00000000;572,98640000;0,00068718;BNB;USD 0,43;USD 622,70;;;;;;;;                        

                $DH_trade =$linha[0];
                $market =$linha[1];
                $tipo =$linha[2];
                $price =$linha[3];
                $amount =$linha[4];
                $total =$linha[5];
                $fee =$linha[6];
                $fee_coin =$linha[7];
                /*
                echo $DH_trade;
                echo $market;
                echo $tipo;
                echo $price;
                echo $amount;
                echo $total;
                echo $fee;
                echo $fee_coin;
                echo'</br>';   
                */
                    
                $time_micro_segundos = strtotime($DH_trade);
                $startTime = str_pad($time_micro_segundos, 13, '0', STR_PAD_RIGHT);

                $url = 'https://api.binance.com/api/v3/klines?interval=1m&limit=1&startTime='.$startTime.'&symbol='.$fee_coin.'USDT';
                $json = file_get_contents($url);
                if($json!=null)
                {	
                    $obj = json_decode($json);
                    $cotacao_fee_coin = $obj[0][1];
                    $fee_dolar = $cotacao_fee_coin * $fee;
                }
                                        
                $sql = "INSERT INTO trade_analisys_binance (DH_trade, market, tipo, price, amount, total, fee, fee_coin, fee_dolar, cotacao_fee_coin) VALUES ('" . $DH_trade. "','".$market."','".$tipo."',". $price.",". $amount.",". $total.",".$fee.",'".$fee_coin."',".$fee_dolar.",".$cotacao_fee_coin.");";
                echo $sql;
                echo'</br>';  
                if (!mysqli_query($conexao, $sql)) 
                {
                    echo 'error 02';
                }
            }    
        }
    }
    /*
    SQL
    ---------------------------------------------------------------------------------------------
    CREATE TABLE `trade_analisys_binance` (
    `cod_ordem` int(11) NOT NULL AUTO_INCREMENT,
    `DH_trade` timestamp NULL DEFAULT NULL,
    `market` varchar(45) DEFAULT NULL,
    `tipo` varchar(45) DEFAULT NULL,
    `price` double DEFAULT NULL,
    `amount` double DEFAULT NULL,
    `total` double DEFAULT NULL,
    `fee` double DEFAULT NULL,
    `fee_coin` varchar(45) DEFAULT NULL,
    `fee_dolar` double DEFAULT NULL,
    `cotacao_fee_coin` varchar(45) DEFAULT NULL,
    UNIQUE KEY `cod_ordem_UNIQUE` (`cod_ordem`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;
    ----------------------------------------------------------------------------------------------------------------------------
    SELECT * FROM trade_analisys_binance;
    ----------------------------------------------------------------------------------------------------------------------------
    SELECT COUNT(DH_trade) FROM trade_analisys_binance;
    ----------------------------------------------------------------------------------------------------------------------------
    SELECT SUM(fee_dolar) FROM trade_analisys_binance;
    ----------------------------------------------------------------------------------------------------------------------------
    delete FROM trade_analisys_binance;
    ALTER TABLE trade_analisys_binance
    AUTO_INCREMENT = 1 ;
    ----------------------------------------------------------------------------------------------------------------------------     
    2021-08-24 00:32:46;ADABUSD;SELL;2.9099;4;11.6396;0.00001754;BNB
    2021-08-23 05:10:16;ADAUSDT;BUY;2.782;4;11.128;0.00001748;BNB
    2021-08-23 05:06:05;BTTUSDT;SELL;0.0045;2473;11.1285;0.00001743;BNB
    ----------------------------------------------------------------------------------------------------------------------------     
    */
?>