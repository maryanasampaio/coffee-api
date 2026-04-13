SELECT u.name, SUM(dl.quantity) AS quantity
FROM drink_logs dl
JOIN users u ON u.id = dl.user_id
WHERE dl.date >= CURDATE() - INTERVAL :days DAY
GROUP BY u.id, u.name
ORDER BY quantity DESC;

SELECT u.name, SUM(dl.quantity) AS quantity
FROM drink_logs dl
JOIN users u ON u.id = dl.user_id
WHERE dl.date = :date
GROUP BY u.id, u.name
ORDER BY quantity DESC;