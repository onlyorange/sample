-- Host: localhost
-- Generation Time: Dec 06, 2013 at 05:57 PM
-- Server version: 5.1.66
-- PHP Version: 5.3.6-13ubuntu3.9

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `game`
--

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

DROP TABLE IF EXISTS `coupons`;
CREATE TABLE IF NOT EXISTS `coupons` (
  `CouponId` int(11) NOT NULL,
  `GameId` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `CouponCode` varchar(50) NOT NULL,
  `DateClaimedUTC` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Coupons';

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

DROP TABLE IF EXISTS `games`;
CREATE TABLE IF NOT EXISTS `games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `StartDate` datetime NOT NULL,
  `blocks` int(1) NOT NULL,
  `swaps` int(1) NOT NULL,
  `round` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='list of games available' AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `prizes`
--

DROP TABLE IF EXISTS `prizes`;
CREATE TABLE IF NOT EXISTS `prizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `GameId` int(11) NOT NULL,
  `Room` int(2) NOT NULL COMMENT '1,2,3 for men,children,women',
  `Photo` varchar(255) NOT NULL COMMENT 'url to image of prize',
  `PhotoSmall` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `PurchaseUrl` varchar(255) NOT NULL,
  `UserId` int(11) NOT NULL COMMENT 'currently holds the prize',
  `Status` int(2) NOT NULL COMMENT 'open (1), blocked (0)',
  `TotalSwaps` int(5) NOT NULL,
  `TotalBlocks` int(5) NOT NULL,
  `TotalViews` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `Room` (`Room`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='prizes and their current holders' AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FacebookId` varchar(100) NOT NULL,
  `FirstName` varchar(255) NOT NULL,
  `LastName` varchar(255) NOT NULL,
  `Initials` varchar(10) NOT NULL,
  `HasPlayed` int(2) NOT NULL COMMENT '0 - no, 1 - yes',
  `OriginalLikeStatus` int(2) NOT NULL COMMENT '0 - none, 1 - liked',
  `DateCreatedUTC` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `MailingInfo` text NOT NULL COMMENT 'serialized mailing info',
  `Newsletter` int(2) NOT NULL COMMENT '1 yes, 0 no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='users' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `users_games`
--

DROP TABLE IF EXISTS `users_games`;
CREATE TABLE IF NOT EXISTS `users_games` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gameid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `roomid` int(11) NOT NULL,
  `prizeid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `datetime` bigint(20) NOT NULL,
  `blocks` int(1) NOT NULL,
  `swaps` int(1) NOT NULL,
  `prizeid_user` int(11) NOT NULL,
  `prizeid_swap` int(11) NOT NULL,
  `pos` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=129 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
