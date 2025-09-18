-- Script para insertar datos de prueba en la tabla pagos
INSERT INTO pagos (metodo_pago, estado_pag, monto, ped_idped, transaccion_id, fecha_pago)
VALUES
('nequi', 'Completado', 50000, 1, 'TXN001', NOW()),
('transferencia', 'Completado', 75000, 2, 'TXN002', NOW()),
('nequi', 'Completado', 25000, 3, 'TXN003', NOW()),
('transferencia', 'Pendiente', 10000, 4, 'TXN004', NOW());
