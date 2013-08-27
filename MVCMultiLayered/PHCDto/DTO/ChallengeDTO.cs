using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.ComponentModel.DataAnnotations;
namespace PHCDto.DTO
{
    public class ChallengeDTO
    {
        public int CHID { get; set; }

        [Required(ErrorMessage = "*")]
        public string ChallengeName { get; set; }

        [Required(ErrorMessage = "*")]
        public string Description { get; set; }

         [Required(ErrorMessage = "*")]
        public string CatDescription { get; set; }

        [Required(ErrorMessage = "*")]
        public string catchImg { get; set; }

        [Required(ErrorMessage = "*")]
        public string dogchImg { get; set; }

        public DateTime ChcreatedDate { get; set; }
        public string SChcreatedDate { get; set; }

        [Required(ErrorMessage = "*")]
        public int BID { get; set; }

        // [Required(ErrorMessage = "*")]
        public string userdesc { get; set; }

         //[Required(ErrorMessage = "*")]
        public string userpetimg { get; set; }

        public int PID { get; set; }

        public string Petname { get; set; }

        public int petid { get; set; }

        public string bname { get; set; }
        public string blogo { get; set; }

        public List<BadgesDTO> lstbadge { get; set; }

        public int pcid { get; set; }

        public int petearn { get; set; }

        public List<ChallengeDTO> lstchdto { get; set; }

        public string chnamehidden { get; set; }

        public Boolean BadgeEarn { get; set; }

        public int cateid { get; set; }

        public string cdescription { get; set; }

        public string ldescription { get; set; }

        public Boolean FBStatus { get; set; }

        public Boolean TwitStatus { get; set; }

        public Boolean PinStatus { get; set; }

        /// <summary>
        /// Whether or not this is contest
        /// </summary>
        public bool IsContestChallenge { get; set; }
        
        /// <summary>
        /// Associated rewardId --if any
        /// </summary>
        public int RewardId { get; set; }

        public string Tagline { get; set; }
    }

    public class badgeDTO
    {
        public int BID { get; set; }

        public int EBID { get; set; }

        [Required(ErrorMessage = "*")]
        public string Badgename { get; set; }

        [Required(ErrorMessage = "*")]
        public string badgeImage { get; set; }

        public string createdDate { get; set; }

        public string bnamehidden { get; set; }

        public string cdescription { get; set; }

        public string ldescription { get; set; }

    }

}
