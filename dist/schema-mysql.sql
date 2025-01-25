-- just a dummy schema

create table `users`
(
    id       int(11) NOT NULL AUTO_INCREMENT,
    username varchar(255) NOT NULL,
    password varchar(255) NOT NULL,

    UNIQUE KEY `username` (`username`),
    PRIMARY KEY (`id`)
);

create table `cities`
(
    id         int(11) NOT NULL AUTO_INCREMENT,
    name       varchar(255) NOT NULL,
    state_code varchar(3) NULL,
    population int(11) NOT NULL,

    PRIMARY KEY (`id`)
);
