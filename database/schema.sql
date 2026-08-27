create table roles (
    id bigint unsigned auto_increment primary key,
    name varchar(32) not null,
    created_at timestamp null,
    updated_at timestamp null,

    constraint roles_name_unique unique (name)
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table users (
    id bigint unsigned auto_increment primary key,
    role_id bigint unsigned not null,
    name varchar(100) not null,
    email varchar(255) not null,
    phone varchar(20) null,
    password varchar(255) not null,
    remember_token varchar(100) null,
    created_at timestamp null,
    updated_at timestamp null,

    constraint users_email_unique unique (email),
    constraint users_role_id_foreign foreign key (role_id) references roles (id) on delete restrict
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table menu_categories (
    id bigint unsigned auto_increment primary key,
    name varchar(100) not null,
    is_active boolean not null default true,
    created_at timestamp null,
    updated_at timestamp null,

    constraint menu_categories_name_unique unique (name)
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table menu_items (
    id bigint unsigned auto_increment primary key,
    menu_category_id bigint unsigned not null,
    name varchar(100) not null,
    description text null,
    price decimal(12,2) not null,
    image_path varchar(255) not null,
    is_active boolean not null default true,
    created_at timestamp null,
    updated_at timestamp null,

    constraint menu_items_category_id_foreign foreign key (menu_category_id) references menu_categories (id) on delete restrict,
    constraint menu_items_price_check check (price > 0)
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table restaurant_tables (
    id bigint unsigned auto_increment primary key,
    code varchar(20) not null,
    capacity tinyint unsigned not null,
    is_active boolean not null default true,
    created_at timestamp null,
    updated_at timestamp null,

    constraint restaurant_tables_code_unique unique (code),
    constraint restaurant_tables_capacity_check check (capacity between 1 and 6)
)engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table time_slots (
    id bigint unsigned auto_increment primary key,
    start_time time not null,
    end_time time not null,
    is_active boolean not null default true,
    created_at timestamp null,
    updated_at timestamp null,

    constraint time_slots_range_unique unique (start_time, end_time),
    constraint time_slots_order_check check (end_time > start_time)
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table reservations (
    id bigint unsigned auto_increment primary key,
    code varchar(40) not null,
    user_id bigint unsigned not null,
    restaurant_table_id bigint unsigned not null,
    time_slot_id bigint unsigned not null,
    reservation_date date not null,
    guest_count tinyint unsigned not null,
    notes varchar(500) null,
    deposit_amount decimal(12,2) not null,
    status enum('pending', 'confirmed', 'completed', 'cancelled', 'no_show') not null default 'pending',
    created_at timestamp null,
    updated_at timestamp null,

    constraint reservations_code_unique unique (code),
    constraint reservations_user_id_foreign foreign key (user_id) references users (id) on delete restrict,
    constraint reservations_table_id_foreign foreign key (restaurant_table_id) references restaurant_tables (id) on delete restrict,
    constraint reservations_slot_id_foreign foreign key (time_slot_id) references time_slots (id) on delete restrict,
    constraint reservations_guest_count_check check (guest_count between 1 and 6),
    constraint reservations_deposit_check check (deposit_amount > 0),

    index reservations_availability_index (reservation_date, time_slot_id, restaurant_table_id, status),
    index reservations_user_date_index (user_id, reservation_date)
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;

create table payments (
    id bigint unsigned auto_increment primary key,
    code varchar(40) not null,
    reservation_id bigint unsigned not null,
    processed_by bigint unsigned not null,
    amount decimal(12,2) not null,
    method enum('cash', 'transfer') not null,
    reference varchar(255) null,
    status enum('paid', 'refunded') not null default 'paid',
    paid_at datetime not null,
    refunded_at datetime null,
    refunded_by bigint unsigned null,
    created_at timestamp null,
    updated_at timestamp null,

    constraint payments_code_unique unique (code),
    constraint payments_reservation_id_unique unique (reservation_id),
    constraint payments_reservation_id_foreign foreign key (reservation_id) references reservations (id) on delete restrict,
    constraint payments_processed_by_foreign foreign key (processed_by) references users (id) on delete restrict,
    constraint payments_refunded_by_foreign foreign key (refunded_by) references users (id) on delete restrict,
    constraint payments_amount_check check (amount > 0),
    constraint payments_refund_state_check check (
        (status = 'paid' and refunded_at is null and refunded_by is null)
        or (status = 'refunded' and refunded_at is not null and refunded_by is not null and refunded_at >= paid_at)
    )
) engine=innodb default charset=utf8mb4 collate=utf8mb4_unicode_ci;
