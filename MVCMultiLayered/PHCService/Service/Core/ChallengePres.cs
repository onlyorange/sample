using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCRepos.Repos.AbstractContracts;
using PHCRepos.Repos.Core;
using PHCDto.DTO;
using PHCService.Service.Core;
using PHCService.Service.InterfaceContracts;


namespace PHCService.Service.Core
{
    public class ChallengePres : IChallengePres
    {
        private IChallengeDbProc _ipres;

        public ChallengePres(IChallengeDbProc view)
        {
            if (view == null) throw new ArgumentNullException();
            _ipres = view;
        }

        #region Challenges

        public string Save_challenge(ChallengeDTO dto)
        {
            return _ipres.Save_challenge(dto);
        }
        public int chalange_count(string cname)
        {
            return _ipres.chalange_count(cname);
        }
        public ChallengeDTO ChkChallengebyDate(DateTime dt)
        {
            return _ipres.ChkChallengebyDate(dt);
        }
        public List<ChallengeDTO> Get_challenges_list()
        {
            return _ipres.Get_challenges_list();
        }
        #endregion

        #region Badges

        public int Badge_count(string bname)
        {
            return _ipres.Badge_count(bname);
        }
        public string Save_badge(badgeDTO dto)
        {
            return _ipres.Save_badge(dto);
        }
        public List<ChallengeDTO> GetBadges()
        {
            return _ipres.GetBadges();
        }
        public List<badgeDTO> GetBadges_list()
        {
            return _ipres.GetBadges_list();
        }

        #endregion

        // Get_challenges_list

        #region User

        public List<FbDTO> users_list()
        {
            return _ipres.users_list();
        }

        #endregion


        public string deleteuser(int id)
        {
            return _ipres.deleteuser(id);
        }

        public badgeDTO Edit_badge(int id)
        {
            return _ipres.Edit_badge(id);
        }

        public string Update_badge(badgeDTO dto)
        {
            return _ipres.Update_badge(dto);
        }
        public string delete_badge(int id)
        {
            return _ipres.delete_badge(id);
        }

        public string delete_challenge(int id)
        {
            return _ipres.delete_challenge(id);
        }

        public ChallengeDTO Edit_challenge(int id)
        {
            return _ipres.Edit_challenge(id);
        }


        public string update_challenge(ChallengeDTO dto)
        {
            return _ipres.update_challenge(dto);
        }

        public string Save_Wallpaper(RewardsDTO adto)
        {
            return _ipres.Save_Wallpaper(adto);
        }

        public List<RewardsDTO> GetWall_list()
        {
            return _ipres.GetWall_list();
        }

        public RewardsDTO Edit_Wall(int id)
        {
            return _ipres.Edit_Wall(id);
        }

        public string Update_Wall(RewardsDTO dto)
        {
            return _ipres.Update_Wall(dto);
        }

        public string delete_wall(int id)
        {
            return _ipres.delete_wall(id);
        }

    }
}
