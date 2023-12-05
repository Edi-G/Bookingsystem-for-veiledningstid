-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 05. Des, 2023 23:44 PM
-- Tjener-versjon: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `veiledning_system`
--

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `assistantteacheravailability`
--

CREATE TABLE `assistantteacheravailability` (
  `AvailabilityID` int(11) NOT NULL,
  `AssistantteacherID` int(11) NOT NULL,
  `Day` varchar(255) NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `assistantteacheravailability`
--

INSERT INTO `assistantteacheravailability` (`AvailabilityID`, `AssistantteacherID`, `Day`, `StartTime`, `EndTime`) VALUES
(2, 5, '2023-12-08', '10:30:00', '12:00:00'),
(3, 5, '2023-12-14', '14:00:00', '16:00:00'),
(4, 5, '2023-12-09', '21:10:00', '22:10:00'),
(5, 5, '2023-12-07', '14:00:00', '15:00:00'),
(6, 5, '2023-12-11', '08:00:00', '09:00:00'),
(7, 5, '2023-12-12', '12:20:00', '16:20:00'),
(8, 5, '2023-12-20', '10:23:00', '16:23:00');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `assistantteachercourses`
--

CREATE TABLE `assistantteachercourses` (
  `ID` int(11) NOT NULL,
  `AssistantTeacherID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `assistantteachercourses`
--

INSERT INTO `assistantteachercourses` (`ID`, `AssistantTeacherID`, `CourseID`) VALUES
(1, 1, 1),
(36, 4, 1),
(187, 5, 1),
(188, 5, 2),
(189, 5, 3);

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `assistantteacherdetails`
--

CREATE TABLE `assistantteacherdetails` (
  `AssistantTeacherID` int(11) NOT NULL,
  `Experience` text NOT NULL,
  `Specializations` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `assistantteacherdetails`
--

INSERT INTO `assistantteacherdetails` (`AssistantTeacherID`, `Experience`, `Specializations`) VALUES
(2, 'Har jobbet som konsulent på deltid', 'Spesialiserer meg i backend, spesefikt PHP, Java, Python'),
(3, 'ASd', '<br />\r\n<b>Warning</b>:  Trying to access array offset on value of type null in <b>C:\\xampp\\htdocs\\prosjekt\\public\\views\\profile.php</b> on line <b>68</b><br />\r\n'),
(4, 'Går master i informatikk', 'Webprogrammering i PHP og DSA'),
(5, '\"En god erfaring\"', '\"En spesiell spesialisering\"');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `bookings`
--

CREATE TABLE `bookings` (
  `BookingID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `AssistantTeacherID` int(11) NOT NULL,
  `CourseID` int(11) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL,
  `Status` enum('confirmed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `bookings`
--

INSERT INTO `bookings` (`BookingID`, `StudentID`, `AssistantTeacherID`, `CourseID`, `StartTime`, `EndTime`, `Status`) VALUES
(69, 6, 5, 2, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'cancelled'),
(70, 6, 5, 2, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'cancelled'),
(71, 6, 5, 2, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'cancelled'),
(72, 6, 5, 2, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'cancelled'),
(73, 6, 5, 1, '2023-12-12 12:20:00', '2023-12-12 12:50:00', 'confirmed'),
(74, 6, 5, 1, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'confirmed'),
(75, 6, 5, 1, '2023-12-12 13:50:00', '2023-12-12 14:20:00', 'confirmed');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `courses`
--

CREATE TABLE `courses` (
  `CourseID` int(11) NOT NULL,
  `CourseName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `courses`
--

INSERT INTO `courses` (`CourseID`, `CourseName`) VALUES
(1, 'Webprogrammering i PHP'),
(2, 'Algoritmer og datastrukturer'),
(3, 'Data Science Applications'),
(4, 'Informasjonssystemsikkerhet');

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `SenderID` int(11) NOT NULL,
  `RecieverID` int(11) NOT NULL,
  `MessageContent` text NOT NULL,
  `Timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `IsRead` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `studentguidance`
--

CREATE TABLE `studentguidance` (
  `StudentBookingID` int(11) NOT NULL,
  `StudentID` int(11) NOT NULL,
  `BookingID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tabellstruktur for tabell `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `FullName` varchar(255) NOT NULL,
  `Role` enum('student','hjelpelærer') NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dataark for tabell `users`
--

INSERT INTO `users` (`UserID`, `Email`, `Password`, `FullName`, `Role`, `CreatedAt`) VALUES
(1, 'roman@omar.com', '$2y$10$Duk.OyOZCnxcFavJgFa0U.W1ZBE91oVRAUsHpgnTSUqasIfC.xi2e', 'Namrah Ram', 'student', '2023-11-20 09:09:19'),
(2, 'ide@damm.com', '$2y$10$yvxs92bY.mVv9tMjUjZ2FOydH8iUIrZ0vb0Os9Ofz/FUX/25869CG', 'Ide BrA', 'hjelpelærer', '2023-11-20 12:07:59'),
(3, 'heisann@live.n', '$2y$10$hgUCcTlHfCed/todh5eUUOzMB9usAeCXZQB555SNE0yx.35oG7ixC', 'Hei Deg', 'hjelpelærer', '2023-11-20 12:21:20'),
(4, 'meg@hotmail.com', '$2y$10$h2c3//IXSgReL.Mi9YrBqeZpKTj2F4gUQnIs6HE/hpo2Sh/3C/Llm', 'Jeg Meg', 'hjelpelærer', '2023-11-22 20:35:02'),
(5, 'malinl@hotmail.com', '$2y$10$RZConEGcNRsFl6rshH0t0ergeRMytBIqKDWszvCDqQ.RvOgs05kT.', 'Malin Legrab', 'hjelpelærer', '2023-12-03 19:12:41'),
(6, 'larsn@hotmail.io', '$2y$10$Sekx/iAijdeI9ANWQLhXme6j8iFrJjPV.41fGKLS1sKHGT2PGY5oy', 'Lars Nerise', 'student', '2023-12-04 22:21:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assistantteacheravailability`
--
ALTER TABLE `assistantteacheravailability`
  ADD PRIMARY KEY (`AvailabilityID`),
  ADD KEY `fk_assistantteacher_availability_assistantteacher` (`AssistantteacherID`);

--
-- Indexes for table `assistantteachercourses`
--
ALTER TABLE `assistantteachercourses`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_assistantteacher_courses_assistantteacher` (`AssistantTeacherID`),
  ADD KEY `fk_assistantteacher_courses_course` (`CourseID`);

--
-- Indexes for table `assistantteacherdetails`
--
ALTER TABLE `assistantteacherdetails`
  ADD PRIMARY KEY (`AssistantTeacherID`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `fk_bookings_student` (`StudentID`),
  ADD KEY `fk_bookings_course` (`CourseID`),
  ADD KEY `fk_bookings_assistantteacher` (`AssistantTeacherID`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`CourseID`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`),
  ADD KEY `fk_messages_users_sender` (`SenderID`),
  ADD KEY `fk_messages_users_reciever` (`RecieverID`);

--
-- Indexes for table `studentguidance`
--
ALTER TABLE `studentguidance`
  ADD PRIMARY KEY (`StudentBookingID`),
  ADD KEY `fk_student_guidance_booking` (`BookingID`),
  ADD KEY `fk_student_guidance_student` (`StudentID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assistantteacheravailability`
--
ALTER TABLE `assistantteacheravailability`
  MODIFY `AvailabilityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `assistantteachercourses`
--
ALTER TABLE `assistantteachercourses`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=190;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `CourseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studentguidance`
--
ALTER TABLE `studentguidance`
  MODIFY `StudentBookingID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Begrensninger for dumpede tabeller
--

--
-- Begrensninger for tabell `assistantteacheravailability`
--
ALTER TABLE `assistantteacheravailability`
  ADD CONSTRAINT `fk_assistantteacher_availability_assistantteacher` FOREIGN KEY (`AssistantteacherID`) REFERENCES `users` (`UserID`);

--
-- Begrensninger for tabell `assistantteachercourses`
--
ALTER TABLE `assistantteachercourses`
  ADD CONSTRAINT `fk_assistantteacher_courses_assistantteacher` FOREIGN KEY (`AssistantTeacherID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_assistantteacher_courses_course` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`CourseID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `assistantteacherdetails`
--
ALTER TABLE `assistantteacherdetails`
  ADD CONSTRAINT `fk_assistantteacher_details_assistantteacher` FOREIGN KEY (`AssistantTeacherID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bookings_assistantteacher` FOREIGN KEY (`AssistantTeacherID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_course` FOREIGN KEY (`CourseID`) REFERENCES `courses` (`CourseID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bookings_student` FOREIGN KEY (`StudentID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_users_reciever` FOREIGN KEY (`RecieverID`) REFERENCES `users` (`UserID`),
  ADD CONSTRAINT `fk_messages_users_sender` FOREIGN KEY (`SenderID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrensninger for tabell `studentguidance`
--
ALTER TABLE `studentguidance`
  ADD CONSTRAINT `fk_student_guidance_booking` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_student_guidance_student` FOREIGN KEY (`StudentID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
