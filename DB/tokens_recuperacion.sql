-- Agregar tabla para tokens de recuperación de contraseña
CREATE TABLE tokens_recuperacion (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    idUsuario INT(11) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expiracion DATETIME NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (idUsuario) REFERENCES usu(idusu) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_expiracion (expiracion)
);
