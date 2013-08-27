using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;


namespace PHCService.Service.InterfaceContracts
{
    public interface IChallengePres
    {

        string Save_challenge(ChallengeDTO adto);

        int chalange_count(string p);



        ChallengeDTO ChkChallengebyDate(DateTime dt);

        string Save_badge(badgeDTO adto);

        int Badge_count(string p);



        List<ChallengeDTO> GetBadges();

        List<FbDTO> users_list();

        List<badgeDTO> GetBadges_list();

        List<ChallengeDTO> Get_challenges_list();

    

        string deleteuser(int id);

        badgeDTO Edit_badge(int id);

        string Update_badge(badgeDTO dto);

        string delete_badge(int id);

        string delete_challenge(int id);

        ChallengeDTO Edit_challenge(int id);

        string update_challenge(ChallengeDTO dto);

        string Save_Wallpaper(RewardsDTO adto);

        List<RewardsDTO> GetWall_list();

        RewardsDTO Edit_Wall(int id);

        string Update_Wall(RewardsDTO dto);

        string delete_wall(int id);
    }
}
