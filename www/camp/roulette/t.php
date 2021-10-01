<?php	
	include "../../event/common/function.php";
	$start = getMillisecond();
	include "../../event/common/db.php";
	include "../../event/common/config.php";

	$sql="

					select CASE
						WHEN	/* 30% 안에 들고 */
								(RAND()*100) <= pct
							THEN
								CASE
									when idx<=80 and b.gift < c.gift then 'gift'	/* gift 5 chance */
									when idx<=85 and b.gift2 < c.gift2 then 'gift2'	/* gift2 coffee */
									when idx<=90 and b.gift3 < c.gift3 then 'gift3'	/* gift3 nPay1000 */
									when idx<=95 and b.gift4 < c.gift4 then 'gift4'	/* gift4 nPay5000 */
									when idx<=100 and b.gift5 < c.gift5 then 'gift5'	/* gift5 cu5000 */
									else 'lose_pct'
								END
							ELSE 'lose_else' END c
					FROM (
							SELECT (RAND()*100) idx
						) a, (
							SELECT
								ifnull(sum(web_gift + mob_gift),0) gift
								, ifnull(sum(web_gift2 + mob_gift2),0) gift2
								, ifnull(sum(web_gift3 + mob_gift3),0) gift3
								, ifnull(sum(web_gift4 + mob_gift4),0) gift4
								, ifnull(sum(web_gift5 + mob_gift5),0) gift5
							FROM tbl_event_sum 
							WHERE reg_dates=left(NOW(), 10)
						) b, (
							select * from (
								SELECT 
									0 g, pct, gift, gift2, gift3, gift4, gift5
								FROM tbl_gift_config where reg_dates = left(now(),10)
								UNION ALL SELECT 1, 40, 10000, 0, 0, 0, 0
							)a where g= case when DATE_FORMAT(now(),'%H') BETWEEN 0 AND 10 then 1 else 0 end
							UNION ALL SELECT 2, 40, 10000, 1, 1, 1, 1 limit 1
						) c
	";

	echo "<table>";
	for($i=0; $i<1000; $i++){
		$rs= db_select($sql);
		echo "<tr><td>$rs[c]</td></tr>";
	}
	echo "<table>";

?>