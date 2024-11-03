CREATE TABLE Users (
    User_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL UNIQUE,
    Password VARCHAR(255) NOT NULL,  -- Store hashed passwords
    date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Barista (
    Barista_ID INT AUTO_INCREMENT PRIMARY KEY,
    Barista_Name VARCHAR(50),
    Barista_Specialty VARCHAR(50),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,  -- Added column to track who updated the barista
    FOREIGN KEY (added_by) REFERENCES Users(User_ID),
    FOREIGN KEY (updated_by) REFERENCES Users(User_ID)  -- Optional foreign key reference
);

CREATE TABLE Coffee (
    Coffee_ID INT AUTO_INCREMENT PRIMARY KEY,
    Coffee_Menu VARCHAR(50),
    Barista_ID INT,
    Coffee_Cost INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,  -- Added column to track who updated the coffee
    FOREIGN KEY (Barista_ID) REFERENCES Barista(Barista_ID),
    FOREIGN KEY (added_by) REFERENCES Users(User_ID),
    FOREIGN KEY (updated_by) REFERENCES Users(User_ID)  -- Optional foreign key reference for updated_by
);
