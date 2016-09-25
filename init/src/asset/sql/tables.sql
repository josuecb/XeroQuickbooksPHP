CREATE TABLE IF NOT EXISTS jc_api_type (
  id       INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
  api_name VARCHAR(255)
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS jc_customers (
  userid         INT UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
  accounting_api INT UNSIGNED,
  realmid        VARCHAR(100),
  company_name   VARCHAR(255) DEFAULT NULL,
  timestamp      VARCHAR(100),
  FOREIGN KEY (accounting_api) REFERENCES jc.jc_api_type (id)
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS jc_credentials (
  userid           INT UNSIGNED NOT NULL PRIMARY KEY,
  accounting_api   INT UNSIGNED,
  oauthTokenSecret TEXT,
  oauthToken       TEXT,
  date_granted     VARCHAR(100),
  FOREIGN KEY (accounting_api) REFERENCES jc_api_type (id)
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS jc_accounting_invoices (
  userid          INT UNSIGNED,
  company_id      TEXT,
  accounting_api  INT UNSIGNED,
  invoice_type    VARCHAR(100),
  company_name    VARCHAR(100),
  txn_date        DATE,
  due_date        DATE,
  txn_status      VARCHAR(255),
  sub_total       VARCHAR(255),
  total_tax       VARCHAR(255),
  total           VARCHAR(255),
  currency_type   VARCHAR(50),
  invoice_id      VARCHAR(255),
  invoice_number  VARCHAR(255),
  amount_due      VARCHAR(255),
  amount_paid     VARCHAR(255),
  amount_credited VARCHAR(255),
  payment_ids     TEXT    DEFAULT NULL,
  invoice_sent    TINYINT DEFAULT 0,
  timestamp       VARCHAR(100),
  FOREIGN KEY (userid) REFERENCES jc_customers (userid),
  FOREIGN KEY (accounting_api) REFERENCES jc_api_type (id)
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;


CREATE TABLE IF NOT EXISTS jc_payments (
  paymentid TEXT NOT NULL,
  date      DATETIME,
  amount    TEXT,
  timestamp TEXT NOT NULL
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS jc_data_requests (
  userid INT UNSIGNED,
  timestamp TEXT NOT NULL
)
  DEFAULT CHARACTER SET utf8
  ENGINE = InnoDB;


ALTER TABLE jc_accounting_invoices
  ADD CONSTRAINT fk_accounting_invoices_user_id FOREIGN KEY (userid) REFERENCES jc_customers (userid);


ALTER TABLE jc_accounting_invoices
  ADD CONSTRAINT fk_accounting_invoices_api_type FOREIGN KEY (accounting_api) REFERENCES jc_api_type (id);


ALTER TABLE jc_customers
  ADD CONSTRAINT fk_costumer_api_type FOREIGN KEY (accounting_api) REFERENCES jc_api_type (id);


ALTER TABLE jc_credentials
  ADD CONSTRAINT fk_credential_api_type FOREIGN KEY (accounting_api) REFERENCES jc_api_type (id);
