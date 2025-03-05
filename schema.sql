create table if not exists users (
    id int primary key auto_increment,
    full_name varchar(255),
    email varchar(255),
    password varchar(255),
    created_at timestamp default CURRENT_TIMESTAMP
);

create table if not exists posts (
    id int primary key auto_increment,
    content text,
    user_id int,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (user_id) references users(id)
);

create table if not exists comments (
    id int primary key auto_increment,
    content text,
    user_id int,
    post_id int,
    created_at timestamp default CURRENT_TIMESTAMP,
    updated_at timestamp default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    foreign key (user_id) references users(id),
    foreign key (post_id) references posts(id)
);

create table if not exists likes (
    id int primary key auto_increment,
    user_id int,
    post_id int,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (user_id) references users(id),
    foreign key (post_id) references posts(id)
);

create table if not exists shares (
    id int primary key auto_increment,
    user_id int,
    post_id int,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (user_id) references users(id),
    foreign key (post_id) references posts(id)
);

create table if not exists friends (
    id int primary key auto_increment,
    friend_user_id int,
    user_id int,
    is_confirmed boolean default false,
    created_at timestamp default CURRENT_TIMESTAMP,
    foreign key (friend_user_id) references users(id),
    foreign key (user_id) references users(id)
);