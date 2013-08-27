using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;

namespace PHCRepos.Repos.AbstractContracts
{
    public abstract class IChallengeDbProc
    {
        public abstract string Save_challenge(ChallengeDTO dto);


        public abstract int chalange_count(string cname);


        public abstract ChallengeDTO ChkChallengebyDate(DateTime dt);

        public abstract string Save_badge(badgeDTO dto);


        public abstract int Badge_count(string bname);



        public abstract List<ChallengeDTO> GetBadges();


        public abstract List<FbDTO> users_list();


        public abstract List<badgeDTO> GetBadges_list();


        public abstract List<ChallengeDTO> Get_challenges_list();


        public abstract string deleteuser(int id);


        public abstract badgeDTO Edit_badge(int id);


        public abstract string Update_badge(badgeDTO dto);


        public abstract string delete_badge(int id);


        public abstract string delete_challenge(int id);


        public abstract ChallengeDTO Edit_challenge(int id);


        public abstract string update_challenge(ChallengeDTO dto);


        public abstract string Save_Wallpaper(RewardsDTO adto);


        public abstract List<RewardsDTO> GetWall_list();


        public abstract RewardsDTO Edit_Wall(int id);


        public abstract string Update_Wall(RewardsDTO dto);


        public abstract string delete_wall(int id);
       
    }
}
