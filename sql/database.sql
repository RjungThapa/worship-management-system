
CREATE TABLE MEMBERS (
    member_id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    email VARCHAR(100),
    phone VARCHAR(10),
    role VARCHAR(50),  
    notes TEXT
);


CREATE TABLE SONGS (
    song_id INT PRIMARY KEY AUTO_INCREMENT,
    song_title VARCHAR(100) NOT NULL,
    hymn_number INT UNIQUE,
    tempo INT,
    time_signature VARCHAR(10),
    key_signature VARCHAR(20),
    lyrics_pdf_path VARCHAR(255),
    notes TEXT
);


CREATE TABLE SERVICES (
    service_id INT PRIMARY KEY AUTO_INCREMENT,
    service_date DATE NOT NULL,
    leader INT,               
    songs_sung TEXT,          
    worship_leader INT,       
    preacher INT,             
    passage VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (leader) REFERENCES MEMBERS(member_id),
    FOREIGN KEY (worship_leader) REFERENCES MEMBERS(member_id),
    FOREIGN KEY (preacher) REFERENCES MEMBERS(member_id)
);