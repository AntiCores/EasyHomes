-- #!sqlite
-- #{easyhomes

-- #  {init
-- #    {homes
CREATE TABLE IF NOT EXISTS homes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    player TEXT COLLATE NOCASE,
    home TEXT,
    x INT,
    y INT,
    z INT,
    yaw FLOAT,
    pitch FLOAT,
    world TEXT
);
-- #    }
-- #    {limit
CREATE TABLE IF NOT EXISTS homecountlimit (
    player TEXT PRIMARY KEY COLLATE NOCASE,
    count INT
);
-- #    }
-- #  }

-- #  {register
-- #    :uuid       string
-- #    :username   string
-- #    :money      float
INSERT INTO economy (
  uuid, username, money
)
VALUES (
  :uuid, :username, :money
)
-- #  }

-- #  {add
-- #    {by-username
-- #        :username   string
-- #        :money      float
-- #        :max        float
UPDATE economy SET money = MIN(money + :money, :max) WHERE username = LOWER(:username);
-- #    }

-- #    {by-uuid
-- #        :uuid   string
-- #        :money  float
-- #        :max    float
UPDATE economy SET money = MIN(money + :money, :max) WHERE uuid = :uuid;
-- #    }
-- #  }

-- #  {deduct
-- #    {by-username
-- #        :username   string
-- #        :money      float
UPDATE economy SET money = MAX(money - :money, 0) WHERE username = LOWER(:username);
-- #    }

-- #    {by-uuid
-- #        :uuid   string
-- #        :money  float
UPDATE economy SET money = MAX(money - :money, 0) WHERE uuid = :uuid;
-- #    }
-- #  }

-- #  {set
-- #    {by-username
-- #        :username   string
-- #        :money      float
-- #        :max        float
UPDATE economy SET money = MIN(:money, :max) WHERE username = LOWER(:username);
-- #    }

-- #    {by-uuid
-- #        :uuid   string
-- #        :money  float
-- #        :max    float
UPDATE economy SET money = MIN(:money, :max) WHERE uuid = :uuid;
-- #    }
-- #  }

-- #  {get
-- #    {by-username
-- #      :username string
SELECT money FROM economy WHERE username = LOWER(:username);
-- #    }

-- #    {by-uuid
-- #      :uuid string
SELECT money FROM economy WHERE uuid = :uuid;
-- #    }

-- #    {top
SELECT * FROM economy ORDER BY money DESC;
-- #    }

-- #    {top10
SELECT * FROM economy ORDER BY money DESC LIMIT 10;
-- #    }
-- #  }

-- #}